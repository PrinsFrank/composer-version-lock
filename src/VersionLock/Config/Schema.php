<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock\Config;

class Schema
{
    public const REQUIRE_KEY                     = 'require';
    public const REQUIRE_DEV_KEY                 = 'require-dev';

    public const EXTRA_KEY                       = 'extra';
    public const COMPOSER_VERSION_CONSTRAINT_KEY = 'composer-version';
    public const COMPOSER_SUGGESTED_VERSION_KEY  = 'composer-suggest';
}