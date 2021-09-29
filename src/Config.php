<?php declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;

final class Config
{
    public function __construct(
        private string $repositoryName,
        private string $repositoryHost,
        private string $repositoryOrganization,
        private array $packageList,
        private ?string $userName,
        private ?string $userEmail,
        private string $accessToken
    ) {
    }
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function getPackageList(): array
    {
        return $this->packageList;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    #[ArrayShape(['type' => "string", 'url' => "string"])]
    public function getPackageRepo(string $packageName): array
    {
        return [
            'type' => 'vcs',
            'url' => 'git@' . $this->repositoryHost . ':' . $this->repositoryOrganization . '/' . $packageName . '.git'
        ];
    }

    public function getRepo(): string
    {
        return $this->repositoryHost . '/' . $this->repositoryName . '.git';
    }

    function setupGitCredentials(): void
    {
        if ($this->getUserName()) {
            exec('git config --global user.name ' . $this->getUserName());
        }

        if ($this->getUserEmail()) {
            exec('git config --global user.email ' . $this->getUserEmail());
        }
        exec('gh config set prompt disabled');
        exec('composer config -g github-oauth.github.com ' . $this->getAccessToken());


    }
}
