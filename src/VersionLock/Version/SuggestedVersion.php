<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Version;

class SuggestedVersion
{
    public static function getForConstraintString(string $constraintString): ?string
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