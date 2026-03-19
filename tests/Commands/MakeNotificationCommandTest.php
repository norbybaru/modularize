<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeNotificationCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_notification()
    {
        $this->artisan(
            command: 'module:make:notification',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Notifications/UserRegistered.php');
    }

    public function test_it_should_fail_to_create_notification_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:notification',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Notifications/UserRegistered.php');

        $this->artisan(
            command: 'module:make:notification',
            parameters: [
                'name' => 'UserRegistered',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Notifications/UserRegistered.php');
    }
}
