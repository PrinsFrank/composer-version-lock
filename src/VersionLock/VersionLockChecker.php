<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PreCommandRunEvent;
use Composer\Semver\Semver;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;

class VersionLockChecker
{
    /** @var IOInterface */
    private $io;

    /** @var IoMessageProvider */
    private $messageProvider;

    public function __construct(IOInterface $io, IoMessageProvider $messageProvider)
    {
        $this->io              = $io;
        $this->messageProvider = $messageProvider;
    }

    /**
     * @throws InvalidComposerVersionException
     * @param CommandEvent|PreCommandRunEvent before version 1.7.0 the PreCommandRunEvent didn't exist
     */
    public function execute($event, VersionLock $versionLock): void
    {
        if ($versionLock->isSatisfiableVersion()) {
            $this->io->write($this->messageProvider->getSuccessMessage($versionLock));
            return;
        }

        if ($event instanceof CommandEvent) {
            /** @var CommandEvent $eventName */
            $eventName = $event->getCommandName();
        }else {
            /** @var PreCommandRunEvent $eventName */
            $eventName = $event->getCommand();
        }

        if (Command::modifiesLockFile($eventName)) {
            $this->io->write($this->messageProvider->getErrorMessage($versionLock));
            throw new InvalidComposerVersionException('Invalid Composer version');
        }

        $this->io->write($this->messageProvider->getWarningMessage($versionLock));
    }
}