<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Helpers;

use Composer\Plugin\CommandEvent;
use Composer\Plugin\PreCommandRunEvent;
use PHPUnit\Framework\TestCase;

class EventHelper extends TestCase
{
    public static function getEventMock()
    {
        if (class_exists(PreCommandRunEvent::class) === false) {
            return (new EventHelper)->createMock(CommandEvent::class);
        }

        return (new EventHelper)->createMock(PreCommandRunEvent::class);
    }

    public static function getGetCommandName(): string
    {
        if (class_exists(PreCommandRunEvent::class) === false) {
            return 'getCommandName';
        }

        return 'getCommand';
    }
}