<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Semver\Comparator;

class VersionLock
{
    /** @var string */
    private $expectedVersion;

    /** @var string */
    private $currentVersion;

    public function __construct(string $expectedVersion, string $currentVersion)
    {
        $this->expectedVersion = $expectedVersion;
        $this->currentVersion = $currentVersion;
    }

    public function isExpectedVersion(): bool
    {
        return Comparator::equalTo($this->expectedVersion, $this->currentVersion);
    }

    public function getRequiredVersion(): string
    {
        return $this->expectedVersion;
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }
}
