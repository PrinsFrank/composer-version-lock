<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock\Output;

use Composer\Composer;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider
 */
class IoMessageProviderTest extends TestCase
{
    /**
     * @covers ::getSuccessMessage
     */
    public function testGetSuccessMessage(): void
    {
        $provider = new IoMessageProvider();
        static::assertSame(
            [
                '<info>Your composer version satisfies the required version set by the current package</info>'
            ],
            $provider->getSuccessMessage(new VersionLock('1.10.15', '1.10.15'))
        );
    }

    /**
     * @covers ::getWarningMessage
     */
    public function testGetWarningMessage(): void
    {
        $provider = new IoMessageProvider();
        static::assertSame(
            [
                '<warning>This package requires composer version 1.10.15</warning>',
                '<comment>-> Continuing as the current action isn\'t modifying the lock file.</comment>'
            ],
            $provider->getWarningMessage(new VersionLock('1.10.14', '1.10.15'))
        );
    }

    /**
     * @covers ::getErrorMessage
     */
    public function testGetErrorMessage(): void
    {
        $provider = new IoMessageProvider();
        static::assertSame(
            [
                '<error>This package requires composer version 1.10.15' .
                ', Currently version is 1.10.14</error>',
                '<comment>To change to the required version, run;</comment>',
                '',
                '    composer self-update 1.10.15',
                ''
            ],
            $provider->getErrorMessage(new VersionLock('1.10.14', '1.10.15'))
        );
    }

    /**
     * @covers ::getErrorMessage
     */
    public function testGetErrorMessageWhenNoSuggestedVersionCanBeDeduced(): void
    {
        $provider = new IoMessageProvider();
        static::assertSame(
            [
                '<error>This package requires composer version 1.10.14 || 1.10.15' .
                ', Currently version is 1.10.13</error>',
                '<comment>To change to the required version, run;</comment>',
                '',
                '    composer self-update {version} <comment>Recommended version could not be deduced</comment>',
                ''
            ],
            $provider->getErrorMessage(new VersionLock('1.10.13', '1.10.14 || 1.10.15'))
        );
    }

    /**
     * @covers ::getMissingConfigMessage
     */
    public function testGetMissingConfigMessage(): void
    {
        $provider = new IoMessageProvider();
        static::assertSame(
            [
                '<error>The "prinsfrank/composer-version-lock" package is required but the required version is not set"</error>',
                '<comment>To use your current version as the new project default, execute;</comment>',
                '',
                '    composer config extra.composer-version ' . Composer::VERSION,
                ''
            ],
            $provider->getMissingConfigMessage()
        );
    }
}