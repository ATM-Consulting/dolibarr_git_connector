<?php

class GitLabInterface extends GitInterface {

	protected const TOKEN_CONST_NAME = 'GIT_GITLAB_TOKEN';
	protected const DEFAULT_OWNER_CONST_NAME = "GIT_GITLAB_DEFAULT_OWNER";
	protected const BASE_API_URL_CONST_NAME = "GIT_GITLAB_BASE_API_URL";

	public function createRepositoryBranch(string $branchName, string $sourceBranchName): array {
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
	public function getBranchSha(string $branchName): string {
		// TODO: Implement getBranchSha() method.
		return "";
	}

	public function getBranches(?string $branchName = null): array {
		// TODO: Implement getBranches() method.
		return [];
	}

	public function getFileContent(string $filePath): string {
		// TODO: Implement getFileContent() method.
		return "";
	}

	public function updateFileContents(string $filePath, string $fileContent, array $optionalInformation = []): void {
		// TODO: Implement updateFileContents() method.
	}
}
