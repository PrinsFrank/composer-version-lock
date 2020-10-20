<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Version;

use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;

class ExpectedVersion
{
    /**
     * @throws MissingConfigException
     */
    public static function getFromExtraConfig(array $extra): string
    {
        if (!array_key_exists(Schema::EXPECTED_COMPOSER_VERSION_KEY, $extra)) {
            throw new MissingConfigException('The Composer version is not set');
        }

        return $extra[Schema::EXPECTED_COMPOSER_VERSION_KEY];
    }
}
