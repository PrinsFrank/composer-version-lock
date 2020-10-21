<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Semver\Semver;

class VersionLock
{
    /** @var string */
    private $versionConstraint;

    /** @var string */
    private $currentVersion;

    public function __construct(string $versionConstraint, string $currentVersion)
    {
        $this->versionConstraint = $versionConstraint;
        $this->currentVersion = $currentVersion;
    }

    public function isSatisfiableVersion(): bool
    {
        return Semver::satisfies($this->currentVersion, $this->versionConstraint);
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    public function getRequiredVersion(): string
    {
        return $this->versionConstraint;
    }
}
