<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeSeederCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_seeder_file()
    {
        $this->artisan(
            command: 'module:make:seeder',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertSeederFile(module: $this->moduleName, filename: 'PostSeeder');
    }

    public function test_it_should_create_database_seeder_file()
    {
        $this->artisan(
            command: 'module:make:seeder',
            parameters: [
                'name' => 'DatabaseSeeder',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertSeederFile(module: $this->moduleName, filename: 'DatabaseSeeder');
    }

    public function test_it_should_create_seeder_file_with_model_option()
    {
        $this->artisan(
            command: 'module:make:seeder',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--model' => 'Post',
            ]
        )
            ->assertSuccessful();

        $this->assertSeederFile(module: $this->moduleName, filename: 'PostSeeder');
    }
}
