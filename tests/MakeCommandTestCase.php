<?php

namespace NorbyBaru\Modularize\Tests;

use Illuminate\Support\Carbon;

abstract class MakeCommandTestCase extends TestCase
{
    public string $moduleName = 'Blog';

    public Carbon $now;

    public function setup(): void
    {
        $this->now = Carbon::now();
        Carbon::setTestNow(testNow: $this->now);
        parent::setUp();
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
        $this->app['files']->deleteDirectory(base_path(config('modularize.root_path')));
    }
}
