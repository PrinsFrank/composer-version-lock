<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Output;

use Composer\Composer;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;

class IoMessageProvider implements MessageProviderInterface
{
    public function getValidVersionMessage(VersionLock $versionLock): array
    {
        return [
            '<info>Your composer version is the same as the one set by the current package</info>'
        ];
    }

    public function getWarningMessage(VersionLock $versionLock): array
    {
        return [
            '<warning>This package expects composer version ' . $versionLock->getRequiredVersion() . '</warning>',
            '<comment>-> Continuing as the current action isn\'t modifying the lock file.</comment>'
        ];
    }

    public function getErrorMessage(VersionLock $versionLock): array
    {
        return [
            '<error>This package requires composer version ' . $versionLock->getRequiredVersion() .
            ', Currently version is ' . $versionLock->getCurrentVersion() . '</error>',
            '<comment>To change to the required version, run;</comment>',
            '',
            '    composer self-update ' . $versionLock->getRequiredVersion(),
            ''
        ];
    }

    public function getMissingConfigMessage(): array
    {
        return [
            '<error>The "prinsfrank/composer-version-lock" package is required but the required version is not set"</error>',
            '<comment>To use your current version as the new project default, execute;</comment>',
            '',
            '    composer ' . Command::CONFIG . ' ' . Schema::EXTRA_KEY . '.' . Schema::EXPECTED_COMPOSER_VERSION_KEY . ' ' . Composer::VERSION,
            ''
        ];
    }
}