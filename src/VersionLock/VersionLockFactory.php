<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Composer;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\SuggestedVersion;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\VersionConstraint;

class VersionLockFactory
{
    public static function createFromComposerInstance(Composer $composer): VersionLock
    {
        return new VersionLock(
            Composer::VERSION,
            VersionConstraint::getFromExtraConfig($composer->getPackage()->getExtra()),
            SuggestedVersion::getFromExtraConfig($composer->getPackage()->getExtra())
        );
    }
}