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
	public function createRepositoryBranch(string $branchName): array {
		// TODO: Implement createRepositoryBranch() method.
		return [];
	}

	/**
	 * @param array $parameters
	 *        - state (string)    => pull requests state: all | closed | open
	 *        - base (string)     => filter on the base branch name: main
	 *        - per_page (int)    => limit number of result: 10
	 *        - page (int)        => page number of result to fetch: 1
	 * @param bool $useDefaultParameters default true, put false to erase default parameters
	 * @return array
	 * @throws ErrorException
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
	public function getBranchUrl(string $branchName): string {
		// TODO: Implement getBranchUrl() method.
		return "";
	}
	public function getBranchSha(string $branchName): string|bool {
		// TODO: Implement getBranchSha() method.
		return "";
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
	 * @throws ErrorException
	 */
	public function getBranches(?string $branchName = null): array {
		$apiEndpoint = "/repos/$this->owner/$this->repository/branches";
		if ($branchName) {
			$apiEndpoint .= "/$branchName";
		}

		$curl = $this->getCurlInstance($apiEndpoint);
		return $this->getCurlResult($curl)['response'];
	}
}
