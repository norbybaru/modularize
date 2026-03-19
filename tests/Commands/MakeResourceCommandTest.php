<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeResourceCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_resource()
    {
        $this->artisan(
            command: 'module:make:resource',
            parameters: [
                'name' => 'UserResource',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Resources/UserResource.php');
    }

    public function test_it_should_create_a_resource_collection()
    {
        $this->artisan(
            command: 'module:make:resource',
            parameters: [
                'name' => 'UserResource',
                '--module' => $this->moduleName,
                '--collection' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Resources/UserResource.php');
    }

    public function test_it_should_fail_to_create_resource_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:resource',
            parameters: [
                'name' => 'UserResource',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Resources/UserResource.php');

        $this->artisan(
            command: 'module:make:resource',
            parameters: [
                'name' => 'UserResource',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Resources/UserResource.php');
    }
}
