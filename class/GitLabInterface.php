<?php

class GitLabInterface extends GitInterface {

	protected const TOKEN_CONST_NAME = 'GIT_GITLAB_TOKEN';
	protected const DEFAULT_OWNER_CONST_NAME = "GIT_GITLAB_DEFAULT_OWNER";
	protected const BASE_API_URL_CONST_NAME = "GIT_GITLAB_BASE_API_URL";

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
