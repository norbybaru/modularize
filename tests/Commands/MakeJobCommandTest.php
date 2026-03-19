<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeJobCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_job()
    {
        $this->artisan(
            command: 'module:make:job',
            parameters: [
                'name' => 'ProcessPodcast',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Jobs/ProcessPodcast.php');
    }

    public function test_it_should_fail_to_create_job_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:job',
            parameters: [
                'name' => 'ProcessPodcast',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Jobs/ProcessPodcast.php');

        $this->artisan(
            command: 'module:make:job',
            parameters: [
                'name' => 'ProcessPodcast',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Jobs/ProcessPodcast.php');
    }
}
