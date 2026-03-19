<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeEventCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_an_event()
    {
        $this->artisan(
            command: 'module:make:event',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Events/SendEmails.php');
    }

    public function test_it_should_fail_to_create_event_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:event',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Events/SendEmails.php');

        $this->artisan(
            command: 'module:make:event',
            parameters: [
                'name' => 'SendEmails',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Events/SendEmails.php');
    }
}
