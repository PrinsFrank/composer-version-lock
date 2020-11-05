<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Output;

use Composer\Composer;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;

class IoMessageProvider implements MessageProviderInterface
{
    public function getSuccessMessage(VersionLock $versionLock): array
    {
        return [
            '<info>Your composer version satisfies the required version set by the current package</info>'
        ];
    }

    public function getWarningMessage(VersionLock $versionLock): array
    {
        return [
            '<warning>This package requires composer version ' . $versionLock->getVersionConstraint() . '</warning>',
            '<comment>-> Continuing as the current action isn\'t modifying the lock file.</comment>'
        ];
    }

    public function getErrorMessage(VersionLock $versionLock): array
    {
        return [
            '<error>This package requires composer version ' . $versionLock->getVersionConstraint() .
            ', Currently version is ' . $versionLock->getCurrentVersion() . '</error>',
            '<comment>To change to the required version, run;</comment>',
            '',
            '    composer self-update ' . ($versionLock->getSuggestedVersion() ?? '{version} <comment>Recommended version could not be deduced</comment>'),
            ''
        ];
    }

    public function getMissingConfigMessage(): array
    {
        return [
            '<error>The "prinsfrank/composer-version-lock" package is required but the required version is not set"</error>',
            '<comment>To use your current version as the new project default, execute;</comment>',
            '',
            '    composer ' . Command::CONFIG . ' ' . Schema::EXTRA_KEY . '.' . Schema::COMPOSER_VERSION_CONSTRAINT_KEY . ' ' . Composer::VERSION,
            ''
        ];
    }

    public function getIncorrectSuggestedVersionMessage(VersionLock $versionLock): array
    {
        return [
            '<error>The suggested version "' . $versionLock->getSuggestedVersion() . '" does not satisfy the version constraint "' . $versionLock->getVersionConstraint() . '"</error>',
            '<comment>Please update the suggested version to one that satisfies the constraint or remove the suggested version</comment>',
            '',
            '    composer ' . Command::CONFIG . ' ' . Schema::EXTRA_KEY . '.' . Schema::COMPOSER_SUGGESTED_VERSION_KEY . ' {version} ',
            ''
        ];
    }
}