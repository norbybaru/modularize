<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeMiddlewareCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_middleware()
    {
        $this->artisan(
            command: 'module:make:middleware',
            parameters: [
                'name' => 'CheckUserRole',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Middleware/CheckUserRole.php');
    }

    public function test_it_should_fail_to_create_middleware_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:middleware',
            parameters: [
                'name' => 'CheckUserRole',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Middleware/CheckUserRole.php');

        $this->artisan(
            command: 'module:make:middleware',
            parameters: [
                'name' => 'CheckUserRole',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Middleware/CheckUserRole.php');
    }
}
