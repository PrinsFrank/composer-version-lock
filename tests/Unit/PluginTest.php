<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit;

use Composer\Composer;
use Composer\IO\ConsoleIO;
use Composer\Package\RootPackage;
use Composer\Plugin\PluginEvents;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\Plugin;
use PrinsFrank\ComposerVersionLock\Tests\Helpers\EventHelper;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * As the Composer factory directly looks for the environment variable to check the contents of the file,
     * we have to overwrite it here and put the correct value back in the
     */
    protected function setUp(): void
    {
        putenv('COMPOSER=' . __DIR__ . '/composer.json');
    }

    protected function tearDown(): void
    {
        putenv('COMPOSER=' . dirname(__DIR__, 2) . '/composer.json');
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        if (defined(PluginEvents::class . '::PRE_COMMAND_RUN') === false) {
            static::assertSame([PluginEvents::COMMAND => 'onPreCommand'], Plugin::getSubscribedEvents());
            return;
        }

        static::assertSame([PluginEvents::PRE_COMMAND_RUN => 'onPreCommand'], Plugin::getSubscribedEvents());
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws MissingConfigException|InvalidComposerVersionException
     */
    public function testOnPreCommandReturnsWhenSettingComposerVersionConfig(): void
    {
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io       = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = EventHelper::getEventMock();
        $event->expects(self::exactly(2))->method('getInput')->willReturn("config 'extra.composer-version'");
        $plugin->onPreCommand($event);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws InvalidComposerVersionException
     */
    public function testOnPreCommandWritesToIoOnMissingConfig(): void
    {
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $package  = $this->createMock(RootPackage::class);
        $package->expects(self::exactly(2))->method('getExtra')->willReturn([]);
        $composer->expects(self::exactly(2))->method('getPackage')->willReturn($package);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = EventHelper::getEventMock();
        $event->expects(self::exactly(4))->method('getInput')->willReturn("update");

        $io->expects(self::once())->method('write')->with(
            [
                '<error>The "prinsfrank/composer-version-lock" plugin is required but the required version is not set</error>',
                '<comment>To use your current version as the new project default, execute;</comment>',
                '',
                '    composer config extra.composer-version ' . Composer::VERSION,
                ''
            ]
        );
        $this->expectException(MissingConfigException::class);
        $this->expectExceptionMessage('Composer version constraint is not set');
        $plugin->onPreCommand($event);
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws MissingConfigException
     */
    public function testOnPreCommandWritesToIoOnIncorrectSuggestedVersion(): void
    {
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $package  = $this->createMock(RootPackage::class);
        $package->expects(self::exactly(2))->method('getExtra')->willReturn(['composer-version' => '1.0.0', 'composer-suggest' => '2.0.0']);
        $composer->expects(self::exactly(2))->method('getPackage')->willReturn($package);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = EventHelper::getEventMock();
        $event->expects(self::exactly(4))->method('getInput')->willReturn("install");

        $io->expects(self::once())->method('write')->with(
            [
                '<error>The suggested version "2.0.0" does not satisfy the version constraint "1.0.0"</error>',
                '<comment>Please update the suggested version to one that satisfies the constraint or remove the suggested version</comment>',
                '',
                '    composer config extra.composer-suggest {version}',
                ''
            ]
        );
        $this->expectException(InvalidComposerVersionException::class);
        $this->expectExceptionMessage('The suggested version is not correct according the version constraint');
        $plugin->onPreCommand($event);
    }

    /**
     * @covers ::activate
     * @covers ::onPreCommand
     * @throws MissingConfigException|InvalidComposerVersionException
     */
    public function testOnPreCommandWritesToIoWhenPassing(): void
    {
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $package  = $this->createMock(RootPackage::class);
        $package->expects(self::exactly(2))->method('getExtra')->willReturn(['composer-version' => '0.0.9']);
        $composer->expects(self::exactly(2))->method('getPackage')->willReturn($package);
        $io = $this->createMock(ConsoleIO::class);
        $plugin->activate($composer, $io);

        $event = EventHelper::getEventMock();
        $event->expects(self::exactly(4))->method('getInput')->willReturn("install");
        $event->expects(self::once())->method(EventHelper::getGetCommandName())->willReturn('install');

        $io->expects(self::once())->method('write')->with(
            [
                '<warning>This package requires composer version 0.0.9</warning>',
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
        $plugin   = new Plugin();
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
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $composer->expects(self::never())->method(self::anything());
        $io = $this->createMock(ConsoleIO::class);
        $plugin->uninstall($composer, $io);
    }
}