<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock\Version;

use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\VersionConstraint;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\Version\VersionConstraint
 */
class VersionConstraintTest extends TestCase
{
    /**
     * @covers ::getFromExtraConfig
     */
    public function testGetFromExtraConfig() : void
    {
        static::assertSame('1.10.15', VersionConstraint::getFromExtraConfig(['composer-version' => '1.10.15']));

        static::assertNull(VersionConstraint::getFromExtraConfig(['composer-version' => null]));
        static::assertNull(VersionConstraint::getFromExtraConfig([]));
        static::assertNull(VersionConstraint::getFromExtraConfig(['foo' => 'bar']));
    }
}