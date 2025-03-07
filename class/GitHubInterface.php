<?php

class GitHubInterface extends GitInterface {

	protected const TOKEN_CONST_NAME = 'GIT_GITHUB_TOKEN';

	public function __construct(string $repo, string $owner, string $baseUrl) {
		parent::__construct($repo, $owner, $baseUrl);

		$gitHubHeaders = [
			'Accept' 				=> 'application/vnd.github+json',
			'X-GitHub-Api-Version'	=> '2022-11-28'
		];
		$this->addHeaders($gitHubHeaders);
	}

	/**
	 * Return new branch information
	 * Throw an error otherwise
	 *
	 * @param string $branchName
	 * @param string $sourceBranchName
	 * @return array
	 * @throws GitException
	 */
	public function createRepositoryBranch(string $branchName, string $sourceBranchName = "main"): array {
		$sourceBranchSha = $this->getBranchSha($sourceBranchName);
		$apiEndpoint = "/repos/$this->owner/$this->repository/git/refs";

		$parameters = [
			"ref" => "refs/heads/$branchName",
			"sha" => $sourceBranchSha
		];
		$additionalOptions = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($parameters)
		];
		$curl = $this->getCurlInstance($apiEndpoint, $additionalOptions);
		$response = $this->getCurlResult($curl);

		return $response['response'];
	}

	/**
	 * @param array $parameters
	 *        - state (string)    => pull requests state: all | closed | open
	 *        - base (string)     => filter on the base branch name: main
	 *        - per_page (int)    => limit number of result: 10
	 *        - page (int)        => page number of result to fetch: 1
	 * @param bool $useDefaultParameters default true, put false to erase default parameters
	 * @return array
	 * @throws GitException
	 */
	public function getRepoPullRequestList(array $parameters = [], bool $useDefaultParameters = true): array {
		$defaultParameters = [
			"state" 	=> "open",
			"base"  	=> "main",
			"per_page" 	=> 10,
			"page"		=> 1
		];
		$parameters = $useDefaultParameters ? array_merge($defaultParameters, $parameters) : $parameters;

		$queryParameters = http_build_query($parameters);
		$apiEndpoint = "/repos/$this->owner/$this->repository/pulls?$queryParameters";

		$curl = $this->getCurlInstance($apiEndpoint);
		return $this->getCurlResult($curl)['response'];
	}

	/**
	 * Retrieve branch API URL if the branch is found
	 * Throw error from getBranch() otherwise
	 *
	 * @param string $branchName
	 * @return string
	 * @throws GitException
	 */
	public function getBranchUrl(string $branchName): string {
		$branchInformation = $this->getBranch($branchName);
		return $branchInformation['commit']['url'];
	}

	/**
	 * Retrieve branch SHA if the branch is found
	 * Throw error from getBranch() otherwise
	 *
	 * @param string $branchName
	 * @return string
	 * @throws GitException
	 */
	public function getBranchSha(string $branchName): string {
		$branchInformation = $this->getBranch($branchName);
		return $branchInformation['commit']['sha'];
	}

	/**
	 * Retrieve all repository branches
	 * Give a specific branch name to get all information about this one particularly
	 *
	 * Returned array of branches, with name, HEAD commit SHA and URL and protected state, for all branches
	 * Returned a lot of information, for specific branch
	 *
	 * @param string|null $branchName
	 * @return array
	 * @throws GitException
	 */
	public function getBranches(?string $branchName = null): array {
		global $langs;

		$apiEndpoint = "/repos/$this->owner/$this->repository/branches";
		if ($branchName) {
			$apiEndpoint .= "/$branchName";
		}

		$curl = $this->getCurlInstance($apiEndpoint);
		$response = $this->getCurlResult($curl);

		if ($response['statusCode'] === self::STATUS_MOVED_PERMANENTLY) {
			if (!isset($response['response']['url'])) {
				$context = [
					"url"	=> $apiEndpoint,
					"method"=> "GET",
					"data"	=> $response,
				];
				throw new GitException($langs->trans('GIT_BAD_REDIRECT_STATUS'), $response['statusCode']);
			}
			$curl = $this->getCurlInstance($response['response']['url']);
			$response = $this->getCurlResult($curl);
		}

		return $response['response'];
	}

	/**
	 * @param string $branchName
	 * @return array
	 * @throws GitException
	 */
	public function getBranch(string $branchName): array {
		return $this->getBranches($branchName);
	}

	/**
	 * Get a file content
	 * We can specify on which commit, tag, or branch we want to get the file content
	 *
	 * @param string $filePath	absolute path to file from repository root
	 * @param string|null $ref	name of commit/tag/branch on which fetch the file - Default: repository's default branch
	 * @return string			file contents
	 * @throws GitException
	 */
	public function getFileContent(string $filePath, ?string $ref = null): string {
		$fileInformation = $this->getContents($filePath, $ref)["response"];
		return base64_decode($fileInformation["content"]);
	}

	/**
	 * Get a file SHA
	 * Throw GitException otherwise
	 *
	 * @param string $filePath
	 * @param string|null $ref
	 * @return string
	 * @throws GitException
	 */
	public function getFileSha(string $filePath, ?string $ref = null): string {
		$fileInformation = $this->getContents($filePath, $ref)["response"];
		return $fileInformation["sha"];
	}

	/**
	 * Update a file content
	 * We can provide some additional parameters to be more precise on the commit
	 * Returned void on success, or throw GitException otherwise
	 *
	 * @param string $filePath
	 * @param string $fileContent
	 * @param array $optionalInformation
	 * 				- string fileSha 	=> SHA of the file - will be fetched if not provided
	 * 				- string branch		=> branch on which to update file - Default: main repository's branch
	 * 				- array committer	=> person that commit the update, that must contain name and email, and can contain a date - Default: authenticated user
	 * 				- array author		=> author of the update, that must contain name and email, and can can contain a date - Default: committer, or authenticated user otherwise
	 * @return void
	 * @throws GitException
	 */
	public function updateFileContents(string $filePath, string $fileContent, array $optionalInformation = []): void {
		$apiEndpoint = "/repos/$this->owner/$this->repository/contents/$filePath";

		$commitMessage = $optionalInformation["commitMessage"] ?? "Update $filePath";
		$fileContent = base64_decode($fileContent, true) ? $fileContent : base64_encode($fileContent); // the content must be base64 encoded, so we encode it if needed
		$fileSha = $optionalInformation["fileSha"] ?? $this->getFileSha($filePath, $optionalInformation["branch"] ?? null);

		$parameters = [
			"message"	=> $commitMessage,
			"content"	=> $fileContent,
			"sha"		=> $fileSha
		];

		if (isset($optionalInformation["branch"])) {
			$parameters["branch"] = $optionalInformation["branch"];
		}
		if (isset($optionalInformation["committer"])) {
			if (isset($optionalInformation["committer"]["name"]) && isset($optionalInformation["committer"]["email"])) {
				$parameters["committer"] = $optionalInformation["committer"];
			}
		}
		if (isset($optionalInformation["author"])) {
			if (isset($optionalInformation["author"]["name"]) && isset($optionalInformation["author"]["email"])) {
				$parameters["author"] = $optionalInformation["author"];
			}
		}

		$additionalOptions = [
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_POSTFIELDS => json_encode($parameters)
		];
		$curl = $this->getCurlInstance($apiEndpoint, $additionalOptions);
		$this->getCurlResult($curl);
	}

	/**
	 * Get contents of a file or a directory in the repository
	 *
	 * @param string $path		path of the file or directory from the repository root
	 * @param string|null $ref	name of the commit/tag/branch on which fetch the content - Default: repository's default branch
	 * @return array			file information or array of file information
	 * @throws GitException
	 */
	private function getContents(string $path, ?string $ref = null): array {
		$apiEndpoint = "/repos/$this->owner/$this->repository/contents/$path";
		if ($ref) {
			$apiEndpoint .= "?ref=$ref";
		}
		$curl = $this->getCurlInstance($apiEndpoint);
		return $this->getCurlResult($curl);
	}
}
