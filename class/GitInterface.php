<?php

abstract class GitInterface implements GitStatusCodeInterface {
	protected const TOKEN_CONST_NAME = "GIT_GIT_TOKEN";
    protected const DEFAULT_OWNER_CONST_NAME = "GIT_GIT_OWNER";
    protected const BASE_API_URL_CONST_NAME = "GIT_GIT_BASE_API_URL";
    protected array $headers = [];
    private static ?string $token = null;

    /**
     * @throws GitException
     */
    public function __construct(
		protected string $repository,
		protected ?string $owner = null,
        protected ?string $baseUrl = null
	) {
		global $langs;
		$langs->load("gitConnector@gitConnector");

		$this->headers = [
			'Authorization' => 'Bearer '.self::getToken(),
			'User-Agent' 	=> 'Dolibarr/GitConnector',
		];

        $this->owner = $owner ?? getDolGlobalString(static::DEFAULT_OWNER_CONST_NAME);
        $this->baseUrl = $baseUrl ?? getDolGlobalString(static::BASE_API_URL_CONST_NAME);

        if (!$this->owner || !$this->baseUrl) {
            throw new GitException($langs->transnoentities("GIT_MISSING_OWNER_OR_BASE_URL"), 500, [
                "defaultOwner"  => $this->owner,
                "baseUrl"       => $this->baseUrl,
            ]);
        }

	}

	public abstract function getRepoPullRequestList(): array;
	public abstract function createRepositoryBranch(string $branchName, string $sourceBranchName): array;
	public abstract function getBranchUrl(string $branchName): string;
	public abstract function getBranchSha(string $branchName): string;
	public abstract function getBranches(?string $branchName = null): array;
	public abstract function getFileContent(string $filePath): string;
	public abstract function updateFileContents(string $filePath, string $fileContent, array $optionalInformation = []): void;

	protected function addHeaders(array $headers): void {
		$this->headers = array_merge($this->headers, $headers);
	}
	protected function getCurlInstance(string $apiEndpoint, array $additionalOptions = []): CurlHandle {
		$curl = curl_init();

		$headers = array_map(fn($key, $value) => "$key: $value", array_keys($this->headers), $this->headers);
		$curlPrivate = $additionalOptions[CURLOPT_POSTFIELDS] ?? [];

		// In some cases, Git directly returns an endpoint to fetch, so the base url of the API is already defined.
		$url = preg_match("#^https?://#", $apiEndpoint) ? $apiEndpoint : $this->baseUrl.$apiEndpoint;
		$curlOptions = [
			CURLOPT_URL 			=> $url,
			CURLOPT_HTTPHEADER 		=> $headers,
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_PRIVATE 		=> $curlPrivate
		];
		$curlOptions += $additionalOptions;
		curl_setopt_array($curl, $curlOptions);

		return $curl;
	}

	/**
	 * @param CurlHandle $curl
	 * @return array
	 * @throws GitException
	 */
	protected function getCurlResult(CurlHandle $curl): array {
		global $langs;

		$response = curl_exec($curl);
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$context = [
			"url"	=> curl_getinfo($curl, CURLINFO_EFFECTIVE_URL),
			"method"=> curl_getinfo($curl, CURLINFO_EFFECTIVE_METHOD),
			"data"	=> curl_getinfo($curl, CURLINFO_PRIVATE)
		];
		if (is_string($context["data"])) {
			$context["data"] = json_decode($context["data"]) ?? $context["data"];
		}
		$responseError = curl_error($curl);

		if ($statusCode === self::STATUS_UNAUTHORIZED) {
			throw new GitException($langs->trans('GIT_UNAUTHORIZED'), $statusCode, $context);
		} elseif ($statusCode >= 400) {
			if ($responseError) {
				$message = $responseError;
			} else {
				$json = json_decode($response, true);
				$message = $json["message"] ?? $response;
			}
			$message = $message ?? $response;

			throw new GitException($langs->trans('GIT_BAD_REQUEST', $message), $statusCode, $context);
		}

		return [
			'response'		=> json_decode($response, true) ?? $response,
			'statusCode'	=> $statusCode,
			'responseError'	=> $responseError,
		];
	}
	protected function setResponseFormat(string $message, int $statusCode): array {
		return [
			'message'	=> $message,
			'statusCode'=> $statusCode
		];
	}
	private static function getToken(): string {
		if (!self::$token) {
			self::$token = getDolGlobalString(static::TOKEN_CONST_NAME);
		}
		return self::$token;
	}
}
