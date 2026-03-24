<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeProviderCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_provider()
    {
        $this->artisan(
            command: 'module:make:provider',
            parameters: [
                'name' => 'AppServiceProvider',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Providers/AppServiceProvider.php');
    }

    public function test_it_should_fail_to_create_provider_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:provider',
            parameters: [
                'name' => 'AppServiceProvider',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Providers/AppServiceProvider.php');

        $this->artisan(
            command: 'module:make:provider',
            parameters: [
                'name' => 'AppServiceProvider',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Providers/AppServiceProvider.php');
    }
}
