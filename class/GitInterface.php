<?php

abstract class GitInterface implements GitStatusCodeInterface {
	protected const TOKEN_CONST_NAME = "GIT_GIT_TOKEN";
	private static ?string $token = null;
	private array $headers = [];

	public function __construct(
		private string $repository,
		private string $owner,
		private string $baseUrl
	) {
		$this->headers = [
			'Authorization' => 'Bearer '.self::getToken(),
			'User-Agent' 	=> 'Git Connector',
		];
	}

	public abstract function getRepoPullRequestList(): array;
	public abstract function createRepositoryBranch(string $branchName): array;
	public abstract function getBranchUrl(string $branchName): string;
	public abstract function getBranchSha(string $branchName): string|bool;

	protected function addHeaders(array $headers): void {
		$this->headers = array_merge($this->headers, $headers);
	}
	private function getCurlInstance(string $apiEndpoint): CurlHandle {
		$curl = curl_init();
		$curlOptions = [
			CURLOPT_URL 			=> $this->baseUrl.$apiEndpoint,
			CURLOPT_HTTPHEADER 		=> $this->headers,
			CURLOPT_RETURNTRANSFER 	=> true
		];
		curl_setopt_array($curl, $curlOptions);

		return $curl;
	}
}
