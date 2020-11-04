<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock;

use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\VersionLock
 */
class VersionLockTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getVersionConstraint
     * @covers ::getCurrentVersion
     */
    public function testProperties(): void
    {
        $versionLock = new VersionLock('1.10.15', '1.10.14');
        static::assertSame('1.10.15', $versionLock->getVersionConstraint());
        static::assertSame('1.10.14', $versionLock->getCurrentVersion());
    }

    /**
     * @covers ::isSatisfiableVersion
     */
    public function testIsSatisfiableVersion(): void
    {
        static::assertFalse((new VersionLock('1.10.15', '1.10.14'))->isSatisfiableVersion());
        static::assertFalse((new VersionLock('1.10.14', '1.10.15'))->isSatisfiableVersion());

        static::assertTrue((new VersionLock('1.10.15', '1.10.15'))->isSatisfiableVersion());
        static::assertTrue((new VersionLock('^1.10.14', '1.10.15'))->isSatisfiableVersion());
        static::assertTrue((new VersionLock('~1.10.14', '1.10.15'))->isSatisfiableVersion());
        static::assertTrue((new VersionLock('1.10.*', '1.10.15'))->isSatisfiableVersion());
        static::assertTrue((new VersionLock('^1', '1.10.15'))->isSatisfiableVersion());
        static::assertTrue((new VersionLock('*', '1.10.15'))->isSatisfiableVersion());
    }
}