<?php

abstract class GitInterface implements GitStatusCodeInterface {
	protected const TOKEN_CONST_NAME = "GIT_GIT_TOKEN";
	private static ?string $token = null;
	protected array $headers = [];

	public function __construct(
		protected string $repository,
		protected string $owner,
		protected string $baseUrl
	) {
		global $langs;
		$langs->load("gitConnector@gitConnector");

		$this->headers = [
			'Authorization' => 'Bearer '.self::getToken(),
			'User-Agent' 	=> 'Dolibarr/GitConnector',
		];
	}

	public abstract function getRepoPullRequestList(): array;
	public abstract function createRepositoryBranch(string $branchName): array;
	public abstract function getBranchUrl(string $branchName): string;
	public abstract function getBranchSha(string $branchName): string;
	public abstract function getBranches(?string $branchName = null): array;

	protected function addHeaders(array $headers): void {
		$this->headers = array_merge($this->headers, $headers);
	}
	protected function getCurlInstance(string $apiEndpoint): CurlHandle {
		$curl = curl_init();

		$headers = array_map(fn($key, $value) => "$key: $value", array_keys($this->headers), $this->headers);

		// In some cases, Git directly returns an endpoint to fetch, so the base url of the API is already defined.
		$url = str_starts_with($apiEndpoint, $this->baseUrl) ? $apiEndpoint : $this->baseUrl.$apiEndpoint;
		$curlOptions = [
			CURLOPT_URL 			=> $url,
			CURLOPT_HTTPHEADER 		=> $headers,
			CURLOPT_RETURNTRANSFER 	=> true
		];
		curl_setopt_array($curl, $curlOptions);

		return $curl;
	}

	/**
	 * @param CurlHandle $curl
	 * @return array
	 * @throws ErrorException
	 */
	protected function getCurlResult(CurlHandle $curl): array {
		global $langs;

		$response = curl_exec($curl);
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$responseError = curl_error($curl);

		if ($statusCode === self::STATUS_UNAUTHORIZED) {
			throw new ErrorException($langs->trans('GIT_UNAUTHORIZED'), $statusCode);
		} elseif ($statusCode >= 400) {
			if ($responseError) {
				$message = $responseError;
			} else {
				$json = json_decode($response, true);
				$message = $json["message"] ?? $response;
			}
			$message = $message ?? $response;

			throw new ErrorException($langs->trans('GIT_BAD_REQUEST', $message), $statusCode);
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
