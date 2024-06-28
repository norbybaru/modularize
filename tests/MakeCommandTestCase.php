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
}
