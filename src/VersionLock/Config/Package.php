<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Config;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;

class Package
{
    public const VENDOR       = 'prinsfrank';
    public const PROJECT_NAME = 'composer-version-lock';
    public const NAME         = self::VENDOR . '/' . self::PROJECT_NAME;

    /**
     * When the composer.json is updated but packages are not updated/removed it might occur that this plugin is not
     * required anymore but still installed in the vendor folder and loaded on composer commands.
     */
    public static function isCurrentlyRequired(IOInterface $io): bool
    {
        $configFile = new JsonFile(Factory::getComposerFile(), null, $io);
        $config = $configFile->read();
        foreach(array_merge($config[Schema::REQUIRE_KEY] ?? [], $config[Schema::REQUIRE_DEV_KEY] ?? []) as $packageName => $version) {
            if ($packageName === self::NAME) {
                return true;
            }
        }

        return false;
    }
}
