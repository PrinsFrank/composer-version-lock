<?php

namespace PrinsFrank\ComposerVersionLock;

use Composer\Composer;
use Composer\Config\JsonConfigSource;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreCommandRunEvent;
use Composer\Semver\Semver;
use PrinsFrank\ComposerVersionLock\VersionLock\Command\Command;
use PrinsFrank\ComposerVersionLock\VersionLock\Config\Schema;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\MissingConfigException;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLockChecker;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLockFactory;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io       = $io;
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
        if (Command::isSettingExpectedComposerVersion($event->getInput())
            || Command::isSettingSuggestedComposerVersion($event->getInput())
            || Command::isRemovingVersionLockPlugin($event->getInput())) {
            return;
        }

        $versionLockConfig = VersionLockFactory::createFromComposerInstance($this->composer);
        if ($versionLockConfig->getVersionConstraint() === null) {
            $this->io->write((new IoMessageProvider())->getMissingConfigMessage());
            throw new MissingConfigException('Composer version constraint is not set');
        }

        if ($versionLockConfig->getSuggestedVersion() !== null
            && !Semver::satisfies($versionLockConfig->getSuggestedVersion(), $versionLockConfig->getVersionConstraint())) {
            $this->io->write((new IoMessageProvider())->getIncorrectSuggestedVersionMessage($versionLockConfig));
            throw new InvalidComposerVersionException('The suggested version is not correct according the version constraint');
        }

        (new VersionLockChecker($this->io, new IoMessageProvider()))->execute($event, $versionLockConfig);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Remove the composer version and suggest from the extra section in the composer file
        $configFile   = new JsonFile(Factory::getComposerFile(), null, $this->io);
        $configSource = new JsonConfigSource($configFile);
        $configSource->removeProperty(Schema::EXTRA_KEY . '.' . Schema::COMPOSER_VERSION_CONSTRAINT_KEY);
        $configSource->removeProperty(Schema::EXTRA_KEY . '.' . Schema::COMPOSER_SUGGESTED_VERSION_KEY);

        if (count($configFile->read()[Schema::EXTRA_KEY]) === 0) {
            $configSource->removeProperty(Schema::EXTRA_KEY);
        }
    }
}
