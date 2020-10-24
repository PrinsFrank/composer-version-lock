<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Version;

use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;

class VersionConstraint
{
    public static function getFromExtraConfig(array $extra): ?string
    {
        return $extra[Schema::COMPOSER_VERSION_CONSTRAINT_KEY] ?? null;
    }
}