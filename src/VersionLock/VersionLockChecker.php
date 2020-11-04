<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\IO\IOInterface;
use Composer\Plugin\PreCommandRunEvent;
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
        $this->io = $io;
        $this->messageProvider = $messageProvider;
    }

    /**
     * @throws InvalidComposerVersionException
     */
    public function execute(PreCommandRunEvent $event, VersionLock $versionLock): void
    {
        if ($versionLock->isSatisfiableVersion()) {
            $this->io->write($this->messageProvider->getSuccessMessage($versionLock));
            return;
        }

        if (Command::modifiesLockFile($event->getCommand())) {
            $this->io->write($this->messageProvider->getErrorMessage($versionLock));
            throw new InvalidComposerVersionException('Invalid Composer version');
        }

        $this->io->write($this->messageProvider->getWarningMessage($versionLock));
    }
}