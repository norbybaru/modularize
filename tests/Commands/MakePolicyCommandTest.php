<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakePolicyCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_a_policy()
    {
        $this->artisan(
            command: 'module:make:policy',
            parameters: [
                'name' => 'PostPolicy',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Policies/PostPolicy.php');
    }

    public function test_it_should_fail_to_create_policy_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:policy',
            parameters: [
                'name' => 'PostPolicy',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Policies/PostPolicy.php');

        $this->artisan(
            command: 'module:make:policy',
            parameters: [
                'name' => 'PostPolicy',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Policies/PostPolicy.php');
    }
}
