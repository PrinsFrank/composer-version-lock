<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Semver\Semver;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\SuggestedVersion;

class VersionLock
{
    /** @var string */
    private $versionConstraint;

    /** @var string */
    private $currentVersion;

    /** @var string|null */
    private $suggestedVersion;

    public function __construct(string $currentVersion, ?string $versionConstraint, ?string $suggestedVersion = null)
    {
        $this->versionConstraint = $versionConstraint;
        $this->currentVersion = $currentVersion;
        $this->suggestedVersion = $suggestedVersion ?? SuggestedVersion::getFromConstraintString($this->getVersionConstraint());
    }

    public function isSatisfiableVersion(): bool
    {
        return Semver::satisfies($this->currentVersion, $this->versionConstraint);
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    public function getVersionConstraint(): ?string
    {
        return $this->versionConstraint;
    }

    public function getSuggestedVersion(): ?string
    {
        return $this->suggestedVersion;
    }
}
