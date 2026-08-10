<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guards the invariant that every test file is actually executed.
 *
 * phpunit.xml.dist used to enumerate test directories one by one, so adding a new
 * directory silently produced tests that no suite ran: tests/App/ held ~30 test files
 * that `composer test` never discovered, which meant reverting the code they covered
 * broke nothing. The config now declares the default suite by exclusion, which makes
 * that state unrepresentable; this test fails if anyone reverts to enumeration and
 * orphans a directory again.
 */
class PhpUnitSuiteCoverageTest extends TestCase
{
    private const DEFAULT_SUFFIX = 'Test.php';

    public function testEveryTestFileBelongsToADeclaredSuite(): void
    {
        $orphans = array_values(array_diff($this->allTestFiles(), $this->filesCoveredBySuites()));
        sort($orphans);

        $this->assertSame(
            [],
            $orphans,
            "These test files are not in any phpunit.xml.dist testsuite, so no one runs them:\n".implode("\n", $orphans)
        );
    }

    public function testEveryDeclaredDirectoryExists(): void
    {
        $missing = [];
        foreach ($this->configuredPaths() as $path) {
            if (!is_dir($this->projectDir().'/'.$path)) {
                $missing[] = $path;
            }
        }
        sort($missing);

        $this->assertSame(
            [],
            $missing,
            "phpunit.xml.dist points at directories that do not exist:\n".implode("\n", $missing)
        );
    }

    private function projectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    /**
     * @return list<string> paths relative to the project dir
     */
    private function allTestFiles(): array
    {
        return $this->collect('tests', self::DEFAULT_SUFFIX);
    }

    /**
     * @return list<string> paths relative to the project dir
     */
    private function filesCoveredBySuites(): array
    {
        $config = simplexml_load_file($this->projectDir().'/phpunit.xml.dist');
        $this->assertNotFalse($config, 'phpunit.xml.dist is not parseable XML');

        $covered = [];
        foreach ($config->testsuites->testsuite as $suite) {
            $included = [];
            foreach ($suite->directory as $directory) {
                $suffix = (string) ($directory['suffix'] ?? '') ?: self::DEFAULT_SUFFIX;
                $included = array_merge($included, $this->collect((string) $directory, $suffix));
            }

            $excluded = [];
            foreach ($suite->exclude as $exclude) {
                $excluded = array_merge($excluded, $this->collect((string) $exclude, self::DEFAULT_SUFFIX));
            }

            $covered = array_merge($covered, array_diff($included, $excluded));
        }

        return array_values(array_unique($covered));
    }

    /**
     * Declared <directory> and <exclude> paths, relative to the project dir.
     *
     * @return list<string>
     */
    private function configuredPaths(): array
    {
        $config = simplexml_load_file($this->projectDir().'/phpunit.xml.dist');
        $this->assertNotFalse($config, 'phpunit.xml.dist is not parseable XML');

        $paths = [];
        foreach ($config->testsuites->testsuite as $suite) {
            foreach ($suite->directory as $directory) {
                $paths[] = (string) $directory;
            }
            foreach ($suite->exclude as $exclude) {
                $paths[] = (string) $exclude;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @return list<string> paths relative to the project dir
     */
    private function collect(string $relativeDir, string $suffix): array
    {
        $absolute = $this->projectDir().'/'.$relativeDir;
        if (!is_dir($absolute)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($absolute, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            /* @var \SplFileInfo $file */
            if ($file->isFile() && str_ends_with($file->getFilename(), $suffix)) {
                $files[] = substr($file->getPathname(), \strlen($this->projectDir()) + 1);
            }
        }

        return $files;
    }
}
