<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Functional;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class VersionConstraintTest extends TestCase
{
    public function testFailsWhenNoVersionSet(): void
    {
        $this->install('no-version-set');
        static::assertStringContainsString(
            'The "prinsfrank/composer-version-lock" package is required but the required version is not set',
            shell_exec('composer update nothing --dry-run')
        );
    }

    private function install(string $scenarioName): ?string
    {
        $command = 'cd ' . __DIR__ . '/scenarios ' .
            '&& rm -rf vendor' .
            '&& rm -f ' . $scenarioName . '.lock' .
            '&& env COMPOSER=' . $scenarioName . '.json composer i';

        return shell_exec($command);
    }
}