<?php declare(strict_types=1);

final class ConfigFactory
{
    /**
     * @var string
     */
    private const GITLAB = 'GITLAB';

    /**
     * @var string
     */
    private const GITHUB = 'GITHUB';

    /**
     * @var string
     */
    private const DEFAULT_BRANCH = 'main';

    /**
     * @var string
     */
    private const DEFAULT_GITLAB_HOST = 'gitlab.com';

    private TokenResolver $tokenResolver;

    public function __construct()
    {
        $this->tokenResolver = new TokenResolver();
    }

    /**
     * @param array<string, mixed> $env
     * @throws Exception
     */
    public function create(array $env): Config
    {
        $ciPlatform = $this->resolvePlatform($env);
        $accessToken = $this->tokenResolver->resolve($env);

        return $this->createFromEnv($env, $accessToken, $ciPlatform);
    }

    /**
     * @param array<string, mixed> $env
     */
    private function resolvePlatform(array $env): string
    {
        return isset($env['GITLAB_CI']) ? self::GITLAB : self::GITHUB;
    }

    /**
     * @param array<string, mixed> $env
     * @throws Exception
     */
    private function createFromEnv(array $env, string $accessToken, string $ciPlatform): Config
    {
        $envPrefix = $ciPlatform === self::GITHUB ? 'INPUT_' : '';

        return new Config(
            repositoryName: $env['GITHUB_REPOSITORY'],
            repositoryHost: $env[$envPrefix . 'REPOSITORY_HOST'] ?? 'github.com',
            repositoryOrganization: $env[$envPrefix . 'REPOSITORY_ORGANIZATION'] ?? throw new \Exception(
                'Repository organization is missing'
            ),
            packageList: json_decode($env[$envPrefix . 'PACKAGE_LIST']),
            userName: $env[$envPrefix . 'USER_NAME'] ?? null,
            userEmail: $env[$envPrefix . 'USER_EMAIL'] ?? null,
            accessToken: $accessToken
        );
    }
}