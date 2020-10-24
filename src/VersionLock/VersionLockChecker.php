<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PreCommandRunEvent;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;

class VersionLockChecker
{
    /** @var IOInterface */
    private $io;

    /** @var VersionLock */
    private $versionLock;

    /** @var IoMessageProvider */
    private $messageProvider;

    public function __construct(string $constraintString, IOInterface $io, IoMessageProvider $messageProvider)
    {
        $this->io = $io;
        $this->messageProvider = $messageProvider;
        $this->versionLock = new VersionLock($constraintString, Composer::VERSION);
    }

    /**
     * @throws InvalidComposerVersionException
     */
    public function execute(PreCommandRunEvent $event): void
    {
        if ($this->versionLock->isSatisfiableVersion()) {
            $this->io->write($this->messageProvider->getSuccessMessage($this->versionLock));
            return;
        }

        if (Command::modifiesLockFile($event->getCommand())) {
            $this->io->write($this->messageProvider->getErrorMessage($this->versionLock));
            throw new InvalidComposerVersionException('Invalid Composer version');
        }

        $this->io->write($this->messageProvider->getWarningMessage($this->versionLock));
    }
}