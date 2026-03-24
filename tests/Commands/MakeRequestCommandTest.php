<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeRequestCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_request()
    {
        $this->artisan(
            command: 'module:make:request',
            parameters: [
                'name' => 'StorePostRequest',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Requests/StorePostRequest.php');
    }

    public function test_it_should_fail_to_create_request_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:request',
            parameters: [
                'name' => 'StorePostRequest',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Requests/StorePostRequest.php');

        $this->artisan(
            command: 'module:make:request',
            parameters: [
                'name' => 'StorePostRequest',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Requests/StorePostRequest.php');
    }
}
