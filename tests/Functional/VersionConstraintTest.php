<?php

namespace PrinsFrank\ComposerVersionLock\Tests\Functional;

use Composer\Composer;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class VersionConstraintTest extends TestCase
{
    /** @var string|null */
    private $currentVersion;

    protected function setUp(): void
    {
        $this->currentVersion = $this->runGetCommandLineVersion();
    }

    public function testFailsWhenNoVersionSet(): void
    {
        $this->runInstall($scenarioName = 'no-version-set');
        static::assertStringContainsString(
            'The "prinsfrank/composer-version-lock" package is required but the required version is not set' . PHP_EOL .
            'To use your current version as the new project default, execute;' . PHP_EOL .
            '' . PHP_EOL .
            '    composer config extra.composer-version ' . $this->currentVersion . PHP_EOL .
            '' . PHP_EOL,
            $this->runModifyingCommand($scenarioName)
        );
    }

    public function testFailsWhenInCorrectSuggestedVersion(): void
    {
        $this->runInstall($scenarioName = 'incorrect-suggested-version');
        static::assertStringContainsString(
            'The suggested version "1.0.0" does not satisfy the version constraint "^2.0.0"' . PHP_EOL .
            'Please update the suggested version to one that satisfies the constraint or remove the suggested version' . PHP_EOL .
            '' . PHP_EOL .
            '    composer config extra.composer-suggest {version}' . PHP_EOL .
            '' . PHP_EOL,
            $this->runModifyingCommand($scenarioName)
        );
    }

    public function testSuccessfulWhenCorrectVersion(): void
    {
        $this->runInstall($scenarioName = 'passing-wildcard-version');
        static::assertStringContainsString(
            'Your composer version satisfies the required version set by the current package' . PHP_EOL .
            '',
            $this->runModifyingCommand($scenarioName)
        );
    }

    public function testWarnsFailsWhenUsingWrongComposerVersion(): void
    {
        $this->runInstall($scenarioName = 'wrong-version');
        static::assertStringContainsString(
            'This package requires composer version 1.0.0, Currently version is ' . $this->currentVersion . PHP_EOL .
            'To change to the required version, run;' . PHP_EOL .
            '' . PHP_EOL .
            '    composer self-update 1.0.0' . PHP_EOL .
            '' . PHP_EOL,
            $this->runModifyingCommand($scenarioName)
        );
        static::assertStringContainsString(
            'This package requires composer version 1.0.0' . PHP_EOL .
            '-> Continuing as the current action isn\'t modifying the lock file.' . PHP_EOL,
            $this->runSafeCommand($scenarioName)
        );
    }

    public function testCleansUpWhenRemovingPackage(): void
    {
        $this->runInstall($scenarioName = 'clean-up');
        $actual = json_decode(file_get_contents(__DIR__ . '/scenarios/' . $scenarioName . '.json'),true);
        $actual['repositories'][0]['url'] = str_replace('composer-version-lock', '',  $actual['repositories'][0]['url']);
        static::assertSame(
            [
                'name' => 'foo/bar',
                'description' => 'Clean up',
                'type' => 'metapackage',
                'minimum-stability' => 'dev',
                'license' => 'MIT',
                'require' => [
                    'prinsfrank/composer-version-lock' => '*',
                    'composer/semver' => '*'
                ],
                'extra' => [
                    'composer-version' => '*',
                    'composer-suggest' => '2.0.10'
                ],
                'repositories' => [
                    [
                        'type' => 'path',
                        'url' => '../../../'
                    ]
                ]
            ],
            $actual
        );
        $this->runRemoveCommand($scenarioName);
        $actual = json_decode(file_get_contents(__DIR__ . '/scenarios/' . $scenarioName . '.json'),true);
        $actual['repositories'][0]['url'] = str_replace('composer-version-lock', '',  $actual['repositories'][0]['url']);
        static::assertSame(
            [
                'name' => 'foo/bar',
                'description' => 'Clean up',
                'type' => 'metapackage',
                'minimum-stability' => 'dev',
                'license' => 'MIT',
                'require' => [
                    'composer/semver' => '*'
                ],
                'repositories' => [
                    [
                        'type' => 'path',
                        'url' => '../../../'
                    ]
                ]
            ],
            $actual
        );
    }

    /**
     * @return string|null When using the Composer::Version constant the installed dependency version nr is returned.
     */
    private function runGetCommandLineVersion(): ?string
    {
        return trim(shell_exec('composer --version | grep -Po \'[0-9]+\.[0-9]+\.[0-9]+\''));
    }

    private function runInstall(string $scenarioName): void
    {
        $command = 'cd ' . __DIR__ . '/scenarios ' .
            '&& rm -rf vendor' .
            '&& rm -f ' . $scenarioName . '.lock' .
            '&& env COMPOSER=' . $scenarioName . '.json composer install';

        shell_exec($command);
    }

    private function runModifyingCommand(string $scenarioName): ?string
    {
        return shell_exec('cd ' . __DIR__ . '/scenarios && env COMPOSER=' . $scenarioName . '.json composer update nothing --dry-run');
    }

    private function runSafeCommand(string $scenarioName): ?string
    {
        return shell_exec('cd ' . __DIR__ . '/scenarios && env COMPOSER=' . $scenarioName . '.json composer validate');
    }

    private function runRemoveCommand(string $scenarioName): ?string
    {
        return shell_exec('cd ' . __DIR__ . '/scenarios && env COMPOSER=' . $scenarioName . '.json composer remove prinsfrank/composer-version-lock');
    }
}