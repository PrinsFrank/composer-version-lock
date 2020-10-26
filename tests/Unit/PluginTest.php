<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit;

use Composer\Composer;
use Composer\IO\ConsoleIO;
use Composer\Package\RootPackage;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreCommandRunEvent;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\Plugin;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        static::assertSame([PluginEvents::PRE_COMMAND_RUN => 'onPreCommand'], Plugin::getSubscribedEvents());
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws MissingConfigException|InvalidComposerVersionException
     */
    public function testOnPreCommandReturnsWhenSettingComposerVersionConfig(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = $this->createMock(PreCommandRunEvent::class);
        $event->expects(self::once())->method('getInput')->willReturn("config 'extra.composer-version'");
        $plugin->onPreCommand($event);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     */
    public function testOnPreCommandWritesToIoOnMissingConfig(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $package = $this->createMock(RootPackage::class);
        $package->expects(self::once())->method('getExtra')->willReturn([]);
        $composer->expects(self::once())->method('getPackage')->willReturn($package);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = $this->createMock(PreCommandRunEvent::class);
        $event->expects(self::once())->method('getInput')->willReturn("update");

        $io->expects(self::once())->method('write')->with(
            [
                '<error>The "prinsfrank/composer-version-lock" package is required but the required version is not set"</error>',
                '<comment>To use your current version as the new project default, execute;</comment>',
                '',
                '    composer config extra.composer-version ' . Composer::VERSION,
                ''
            ]
        );
        $this->expectException(MissingConfigException::class);
        $this->expectExceptionMessage('Composer version not set');
        $plugin->onPreCommand($event);
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws MissingConfigException|InvalidComposerVersionException
     */
    public function testOnPreCommandWritesToIoWhenPassing(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $package = $this->createMock(RootPackage::class);
        $package->expects(self::once())->method('getExtra')->willReturn(['composer-version' => '1.0.0']);
        $composer->expects(self::once())->method('getPackage')->willReturn($package);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = $this->createMock(PreCommandRunEvent::class);
        $event->expects(self::once())->method('getInput')->willReturn("install");
        $event->expects(self::once())->method('getCommand')->willReturn('install');

        $io->expects(self::once())->method('write')->with(
            [
                '<warning>This package requires composer version 1.0.0</warning>',
                '<comment>-> Continuing as the current action isn\'t modifying the lock file.</comment>'
            ]
        );
        $plugin->onPreCommand($event);
    }

    /**
     * @covers ::deactivate
     */
    public function testDeactivate(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $composer->expects(self::never())->method(self::anything());
        $io = $this->createMock(ConsoleIO::class);
        $plugin->deactivate($composer, $io);
    }

    /**
     * @covers ::uninstall
     */
    public function testUninstall(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $composer->expects(self::never())->method(self::anything());
        $io = $this->createMock(ConsoleIO::class);
        $plugin->deactivate($composer, $io);
    }
}