<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock;

use Composer\Composer;
use Composer\Package\RootPackage;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLockFactory;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\VersionLockFactory
 */
class VersionLockFactoryTest extends TestCase
{
    /**
     * @covers ::createFromComposerInstance
     */
    public function testCreateFromComposerInstance(): void
    {
        $composer = $this->createMock(Composer::class);
        $package = $this->createMock(RootPackage::class);
        $composer->expects(self::exactly(2))->method('getPackage')->willReturn($package);
        $package->expects(self::exactly(2))->method('getExtra')->willReturn(
            [
                Schema::COMPOSER_VERSION_CONSTRAINT_KEY => '1.10.15',
                Schema::COMPOSER_SUGGESTED_VERSION_KEY => '1.10.16'
            ]
        );

        static::assertEquals(
            new VersionLock(Composer::VERSION, '1.10.15', '1.10.16'),
            VersionLockFactory::createFromComposerInstance($composer)
        );
    }
}