<?php

namespace NorbyBaru\Modularize\Tests;

use NorbyBaru\Modularize\ModularizeServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function getModulePath(string $module): string
    {
        return base_path(config('modularize.root_path')."/$module");
    }

    protected function getPackageProviders($app)
    {
        return [
            ModularizeServiceProvider::class,
        ];
    }
}
