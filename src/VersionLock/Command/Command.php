<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Command;

use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;

class Command
{
    public const UPDATE = 'update';
    public const REMOVE = 'remove';
    public const REQUIRE = 'require';
    public const CONFIG = 'config';

    /**
     * @var array What commands update the composer.lock file
     */
    private const MODIFIES_LOCK = [
        self::UPDATE,
        self::REMOVE,
        self::REQUIRE,
        self::CONFIG
    ];

    public static function modifiesLockFile(string $command): bool
    {
        return in_array($command, static::MODIFIES_LOCK, true);
    }

    public static function isSettingExpectedComposerVersion(string $input): bool
    {
        return strpos($input, "config '" . Schema::EXTRA_KEY . "." . Schema::EXPECTED_COMPOSER_VERSION_KEY . "'") === 0;
    }
}