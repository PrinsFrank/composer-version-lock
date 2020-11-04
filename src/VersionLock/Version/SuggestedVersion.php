<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Version;

use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;

class SuggestedVersion
{
    public static function getFromExtraConfig(array $extra): ?string
    {
        return $extra[Schema::COMPOSER_SUGGESTED_VERSION_KEY] ?? null;
    }

    public static function getFromConstraintString(string $constraintString): ?string
    {
        if (preg_match('/[ ,!=<>*^-~]/', $constraintString) === 0) {
            return $constraintString;
        }

        $versionWithoutCaretTilde = ltrim($constraintString, '~^');
        if (preg_match('/^[\d]+.[\d]+.[\d]+$/', $versionWithoutCaretTilde) === 1) {
            return $versionWithoutCaretTilde;
        }

        return null;
    }
}