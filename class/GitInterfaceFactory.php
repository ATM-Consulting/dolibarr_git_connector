<?php

require_once __DIR__."/../core/lib/autoloader.php";

class GitInterfaceFactory {

	public static function create(GitPlatform $platform, $repo, $owner, $baseUrl): GitInterface {
		return match ($platform) {
			GitPlatform::GITHUB	=> new GitHubInterface($repo, $owner, $baseUrl),
			GitPlatform::GITLAB	=> new GitLabInterface($repo, $owner, $baseUrl),
		};
	}

}
