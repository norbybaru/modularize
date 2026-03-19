<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeListenerCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_listener()
    {
        $this->artisan(
            command: 'module:make:listener',
            parameters: [
                'name' => 'SendEmailsListener',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Listeners/SendEmailsListener.php');
    }

    public function test_it_should_fail_to_create_listener_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:listener',
            parameters: [
                'name' => 'SendEmailsListener',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Listeners/SendEmailsListener.php');

        $this->artisan(
            command: 'module:make:listener',
            parameters: [
                'name' => 'SendEmailsListener',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Listeners/SendEmailsListener.php');
    }
}
