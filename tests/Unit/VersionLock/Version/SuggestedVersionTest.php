<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Unit\VersionLock\Version;

use PHPUnit\Framework\TestCase;
use PrinsFrank\ComposerVersionLock\VersionLock\Version\SuggestedVersion;

/**
 * @coversDefaultClass \PrinsFrank\ComposerVersionLock\VersionLock\Version\SuggestedVersion
 */
class SuggestedVersionTest extends TestCase
{
    /**
     * @covers ::getFromConstraintString
     */
    public function testGetForConstraintStringWhenSimpleConstraint(): void
    {
        static::assertSame('1.10.15', SuggestedVersion::getFromConstraintString('1.10.15'));
    }

    /**
     * @covers ::getFromConstraintString
     */
    public function testGetForConstraintStringWhenCaretOrTildeConstraint(): void
    {
        static::assertSame('1.10.15', SuggestedVersion::getFromConstraintString('~1.10.15'));
        static::assertSame('1.10.15', SuggestedVersion::getFromConstraintString('^1.10.15'));
    }

    /**
     * @covers ::getFromConstraintString
     */
    public function testGetForConstraintStringWhenOtherConstraint(): void
    {
        static::assertNull(SuggestedVersion::getFromConstraintString('1.10.14 || 1.10.15'));
        static::assertNull(SuggestedVersion::getFromConstraintString('1.10.14 - 1.10.15'));
    }
}