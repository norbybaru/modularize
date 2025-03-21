<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeConsoleCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_console_command()
    {
        $this->artisan(
            command: 'module:make:console',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Console/SendEmails.php');
    }

    public function test_it_should_create_a_console_command_with_force_option()
    {
        $this->artisan(
            command: 'module:make:console',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Console/SendEmails.php');

        $this->artisan(
            command: 'module:make:console',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
                '--force' => true,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Console/SendEmails.php');
    }

    public function test_it_should_fail_to_create_console_command_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:console',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Console/SendEmails.php');

        $this->artisan(
            command: 'module:make:console',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Console/SendEmails.php');
    }
}
