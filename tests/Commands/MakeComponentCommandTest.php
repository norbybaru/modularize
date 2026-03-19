<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeComponentCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_component()
    {
        $this->artisan(
            command: 'module:make:component',
            parameters: [
                'name' => 'UserCard',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Components/UserCard.php');
    }

    public function test_it_should_create_a_component_with_force_option()
    {
        $this->artisan(
            command: 'module:make:component',
            parameters: [
                'name' => 'UserCard',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Components/UserCard.php');

        $this->artisan(
            command: 'module:make:component',
            parameters: [
                'name' => 'UserCard',
                '--module' => $this->moduleName,
                '--force' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Components/UserCard.php');
    }

    public function test_it_should_fail_to_create_component_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:component',
            parameters: [
                'name' => 'UserCard',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Components/UserCard.php');

        $this->artisan(
            command: 'module:make:component',
            parameters: [
                'name' => 'UserCard',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Components/UserCard.php');
    }
}

