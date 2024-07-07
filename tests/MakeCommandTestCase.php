<?php

namespace NorbyBaru\Modularize\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;

abstract class MakeCommandTestCase extends TestCase
{
    public string $moduleName = 'Blog';

    public Carbon $now;

    protected Filesystem $files;

    public function setup(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->now = Carbon::now();
        Carbon::setTestNow(testNow: $this->now);
        $this->cleanUp();
    }

    public function teardown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    public function getModulePath(?string $module = null): string
    {
        $module = $module ?? $this->moduleName;

        return parent::getModulePath($module);
    }

    public function cleanUp(): void
    {
        $this->files->deleteDirectory(base_path(config('modularize.root_path')));
    }

    protected function assertMigrationFile(string $module, string $migrationFilename): void
    {
        $migrations = $this->files->allFiles(directory: $this->getModulePath($module).'/Database/migrations');
        $this->assertNotEmpty(actual: $migrations);
        $this->assertEquals(
            expected: 1,
            actual: count($migrations)
        );

        /** @var \Symfony\Component\Finder\SplFileInfo */
        $migrationFile = $migrations[0];
        $this->assertStringContainsString(
            needle: $migrationFilename,
            haystack: $migrationFile->getFilename()
        );
    }

    protected function getGeneratedClassMethods(string $subjectFile): array
    {
        // Define the regex pattern to match the entire function signature, excluding the opening {
        $pattern = '/function\s+([a-zA-Z_]\w*)\s*\(([^)]*)\)\s*(?=\{)/';
        preg_match_all(pattern: $pattern, subject: $subjectFile, matches: $match);

        if (empty($match[0])) {
            $this->fail('No function found in controller');
        }

        foreach ($match[0] as $function) {
            $methods[] = trim($function);
        }

        return $methods;
    }
}
