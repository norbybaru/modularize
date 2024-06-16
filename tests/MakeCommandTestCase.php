<?php

namespace NorbyBaru\Modularize\Tests;

abstract class MakeCommandTestCase extends TestCase
{
    public string $moduleName = 'Blog';

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
        $this->app['files']->deleteDirectory($this->getModulePath(module: $this->moduleName));
    }
}
