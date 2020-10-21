<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Version;

use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;

class VersionConstraint
{
    /**
     * @throws MissingConfigException
     */
    public static function getFromExtraConfig(array $extra): string
    {
        if (!array_key_exists(Schema::COMPOSER_VERSION_CONSTRAINT_KEY, $extra)) {
            throw new MissingConfigException('The Composer version is not set');
        }

        return $extra[Schema::COMPOSER_VERSION_CONSTRAINT_KEY];
    }
}