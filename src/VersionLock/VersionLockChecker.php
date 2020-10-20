<?php

namespace PrinsFrank\ComposerVersionLock\VersionLock;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Plugin\PreCommandRunEvent;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\ExpectedVersion;

class VersionLockChecker
{
    /** @var IOInterface */
    private $io;

    /** @var VersionLock */
    private $versionLock;

    /** @var IoMessageProvider */
    private $messageProvider;

    public function __construct(string $expectedVersion, IOInterface $io, IoMessageProvider $messageProvider)
    {
        $this->io = $io;
        $this->messageProvider = $messageProvider;
        $this->versionLock = new VersionLock($expectedVersion, Composer::VERSION);
    }

    public function execute(PreCommandRunEvent $event): void
    {
        if ($this->versionLock->isExpectedVersion()) {
            $this->io->write($this->messageProvider->getValidVersionMessage($this->versionLock));
            return;
        }

        if (Command::modifiesLockFile($event->getCommand())) {
            $this->io->write($this->messageProvider->getErrorMessage($this->versionLock));
            exit;
        }

        $this->io->write($this->messageProvider->getWarningMessage($this->versionLock));
    }
}