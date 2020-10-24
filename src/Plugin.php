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
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\VersionConstraint;
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

    /**
     * @throws MissingConfigException
     * @throws InvalidComposerVersionException
     */
    public function onPreCommand(PreCommandRunEvent $event): void
    {
        if (Command::isSettingExpectedComposerVersion($event->getInput())) {
            return;
        }

        $constraintString = VersionConstraint::getFromExtraConfig($this->composer->getPackage()->getExtra());
        if ($constraintString === null) {
            $this->io->write((new IoMessageProvider())->getMissingConfigMessage());
            throw new MissingConfigException('Composer version not set');
        }

        (new VersionLockChecker($constraintString, $this->io, new IoMessageProvider()))->execute($event);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}
