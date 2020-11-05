<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock\Command;

use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\Command\Command
 */
class CommandTest extends TestCase
{
    /**
     * @covers ::modifiesLockFile
     */
    public function testModifiesLockFile(): void
    {
        static::assertTrue(Command::modifiesLockFile('update'));
        static::assertTrue(Command::modifiesLockFile('remove'));
        static::assertTrue(Command::modifiesLockFile('require'));

        static::assertFalse(Command::modifiesLockFile('config'));
        static::assertFalse(Command::modifiesLockFile('install'));
    }

    /**
     * @covers ::isSettingExpectedComposerVersion
     */
    public function testIsSettingExpectedComposerVersion(): void
    {
        static::assertTrue(Command::isSettingExpectedComposerVersion("config 'extra.composer-version'"));

        static::assertFalse(Command::isSettingExpectedComposerVersion("config 'extra.fooBar'"));
        static::assertFalse(Command::isSettingExpectedComposerVersion("config"));
        static::assertFalse(Command::isSettingExpectedComposerVersion("install"));
    }

    /**
     * @covers ::isSettingSuggestedComposerVersion
     */
    public function testIsSettingSuggestedComposerVersion(): void
    {
        static::assertTrue(Command::isSettingSuggestedComposerVersion("config 'extra.composer-suggest'"));

        static::assertFalse(Command::isSettingSuggestedComposerVersion("config 'extra.fooBar'"));
        static::assertFalse(Command::isSettingSuggestedComposerVersion("config"));
        static::assertFalse(Command::isSettingSuggestedComposerVersion("install"));
    }
}