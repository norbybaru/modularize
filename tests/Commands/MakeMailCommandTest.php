<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeMailCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_mail()
    {
        $this->artisan(
            command: 'module:make:mail',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Mail/UserRegistered.php');
    }

    public function test_it_should_fail_to_create_mail_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:mail',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Mail/UserRegistered.php');

        $this->artisan(
            command: 'module:make:mail',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Mail/UserRegistered.php');
    }
}
