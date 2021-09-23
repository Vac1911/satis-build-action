<?php declare(strict_types=1);

final class Config
{
    public function __construct(
        private string $packageDirectory,
        private string $repositoryHost,
        private string $repositoryOrganization,
        private array $packageList,
        private ?string $userName,
        private ?string $userEmail,
        private string $accessToken
    ) {
    }

    public function getPackageDirectory(): string
    {
        return $this->packageDirectory;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function getPackageList(): array
    {
        return $this->packageList;
    }

    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getGitRepository(): string
    {
        return $this->repositoryHost . '/' . $this->repositoryOrganization . '/' . $this->repositoryName . '.git';
    }

    public function getPackageRepo(string $packageName): array
    {
        return [
            'type' => 'vcs',
            'url' => 'git@' . $this->repositoryHost . ':' . $this->repositoryOrganization . '/' . $packageName . '.git'
        ];
    }

    function setupGitCredentials(): void
    {
        if ($this->getUserName()) {
            exec('git config --global user.name ' . $this->getUserName());
        }

        if ($this->getUserEmail()) {
            exec('git config --global user.email ' . $this->getUserEmail());
        }
    }
}
