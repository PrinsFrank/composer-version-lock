<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Output;

use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;

interface MessageProviderInterface
{
    public function getPluginNotRequiredMessage(): array;

    public function getSuccessMessage(VersionLock $versionLock): array;

    public function getWarningMessage(VersionLock $versionLock): array;

    public function getErrorMessage(VersionLock $versionLock): array;

    public function getMissingConfigMessage(): array;

    public function getIncorrectSuggestedVersionMessage(VersionLock $versionLock): array;
}