<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock\Config;

use Composer\Factory;
use Composer\IO\ConsoleIO;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Package;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\Config\Package
 */
class PackageTest extends TestCase
{
    /**
     * @covers ::isCurrentlyRequired
     */
    public function testIsCurrentlyRequiredWhenNotRequired(): void
    {
        $io = $this->createMock(ConsoleIO::class);
        static::assertFalse(Package::isCurrentlyRequired($io));
    }

    /**
     * @covers ::isCurrentlyRequired
     */
    public function testIsCurrentlyRequiredWhenRequired(): void
    {
        $composerFileBackup = Factory::getComposerFile();
        putenv('COMPOSER=' . dirname(__DIR__, 2). '/composer.json');

        $io = $this->createMock(ConsoleIO::class);
        static::assertTrue(Package::isCurrentlyRequired($io));
        putenv('COMPOSER=' . $composerFileBackup);
    }
}
