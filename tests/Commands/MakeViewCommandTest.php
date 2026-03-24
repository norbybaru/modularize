<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeViewCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_view()
    {
        $this->artisan(
            command: 'module:make:view',
            parameters: [
                'name' => 'index',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Views/index.blade.php');
    }

    public function test_it_should_fail_to_create_view_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:view',
            parameters: [
                'name' => 'index',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Views/index.blade.php');

        $this->artisan(
            command: 'module:make:view',
            parameters: [
                'name' => 'index',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Views/index.blade.php');
    }
}
