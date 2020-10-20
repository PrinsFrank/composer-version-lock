<?php

namespace PrinsFrank\ComposerVersionLock;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreCommandRunEvent;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\ExpectedVersion;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLockChecker;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @uses onPreCommand
     */
    public static function getSubscribedEvents(): array
    {
        return [PluginEvents::PRE_COMMAND_RUN => 'onPreCommand'];
    }

    public function onPreCommand(PreCommandRunEvent $event): void
    {
        if (Command::isSettingExpectedComposerVersion($event->getInput())) {
            return;
        }

        try {
            $expectedVersion = ExpectedVersion::getFromExtraConfig($this->composer->getPackage()->getExtra());
        } catch (MissingConfigException $e) {
            $this->io->write((new IoMessageProvider())->getMissingConfigMessage());
            exit;
        }

        (new VersionLockChecker($expectedVersion, $this->io, new IoMessageProvider()))->execute($event);
    }
}
