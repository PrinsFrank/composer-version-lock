<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock;

use Composer\Composer;
use Composer\IO\ConsoleIO;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PreCommandRunEvent;
use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\Tests\Helpers\EventHelper;
use PrinsFrank\ComposerVersionLock\VersionLock\Exception\InvalidComposerVersionException;
use PrinsFrank\ComposerVersionLock\VersionLock\Output\IoMessageProvider;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLock;
use PrinsFrank\ComposerVersionLock\VersionLock\VersionLockChecker;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\VersionLockChecker
 * @covers ::__construct
 */
class VersionLockCheckerTest extends TestCase
{
    /**
     * @covers ::execute
     * @throws InvalidComposerVersionException
     */
    public function testExecuteWithCorrectVersion(): void
    {
        $io              = $this->createMock(ConsoleIO::class);
        $messageProvider = $this->createMock(IoMessageProvider::class);
        $checker         = new VersionLockChecker($io, $messageProvider);

        $event = EventHelper::getEventMock();
        $messageProvider->expects(self::once())->method('getSuccessMessage')->willReturn(['fooBar']);
        $io->expects(self::once())->method('write')->with(['fooBar']);
        $checker->execute($event, new VersionLock(Composer::VERSION, Composer::VERSION));
    }

    /**
     * @covers ::execute
     */
    public function testExecuteWithInCorrectVersionUpdatingCommand(): void
    {
        $io              = $this->createMock(ConsoleIO::class);
        $messageProvider = $this->createMock(IoMessageProvider::class);
        $checker         = new VersionLockChecker($io, $messageProvider);

        $event = EventHelper::getEventMock();
        $event->expects(self::once())->method(EventHelper::getGetCommandName())->willReturn('update');
        $messageProvider->expects(self::once())->method('getErrorMessage')->willReturn(['fooBar']);
        $io->expects(self::once())->method('write')->with(['fooBar']);
        $this->expectException(InvalidComposerVersionException::class);
        $this->expectExceptionMessage('Invalid Composer version');
        $checker->execute($event, new VersionLock(Composer::VERSION, '0.0.1'));
    }

    /**
     * @covers ::execute
     * @throws InvalidComposerVersionException
     */
    public function testExecuteWithInCorrectVersionNonUpdatingCommand(): void
    {
        $io              = $this->createMock(ConsoleIO::class);
        $messageProvider = $this->createMock(IoMessageProvider::class);
        $checker         = new VersionLockChecker($io, $messageProvider);

        $event = EventHelper::getEventMock();
        $event->expects(self::once())->method(EventHelper::getGetCommandName())->willReturn('install');
        $messageProvider->expects(self::once())->method('getWarningMessage')->willReturn(['fooBar']);
        $io->expects(self::once())->method('write')->with(['fooBar']);
        $checker->execute($event, new VersionLock(Composer::VERSION, '0.0.1'));
    }
}