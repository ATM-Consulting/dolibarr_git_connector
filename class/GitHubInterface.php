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
	public function getRepoPullRequestList(): array {
		// TODO: Implement getRepoPullRequestList() method.
		return [];
	}
	public function getRepoPullRequestsList(): array {
		// TODO: Implement getRepoPullRequestsList() method.
		return [];
	}
	public function getBranchUrl(string $branchName): string {
		// TODO: Implement getBranchUrl() method.
		return "";
	}
	public function getBranchSha(string $branchName): string|bool {
		// TODO: Implement getBranchSha() method.
		return "";
	}
}
