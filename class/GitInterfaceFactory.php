<?php

require_once __DIR__."/../core/lib/autoloader.php";

class GitInterfaceFactory {

    /**
     * @throws GitException
     */
    public static function create(GitPlatform $platform, string $repo, ?string $owner = null, ?string $baseUrl = null): GitInterface {
		return match ($platform) {
			GitPlatform::GITHUB	=> new GitHubInterface($repo, $owner, $baseUrl),
			GitPlatform::GITLAB	=> new GitLabInterface($repo, $owner, $baseUrl),
		};
	}

}
