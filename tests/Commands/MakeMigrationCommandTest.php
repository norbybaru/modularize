<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeMigrationCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_migration_file()
    {
        $this->artisan(
            command: 'module:make:migration',
            parameters: [
                'name' => 'create_posts_table',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'create_posts_table.php');
    }

    public function test_it_should_create_migration_file_with_create_option()
    {
        $this->artisan(
            command: 'module:make:migration',
            parameters: [
                'name' => 'create_posts_table',
                '--module' => $this->moduleName,
                '--create' => 'posts',
            ]
        )
            ->assertSuccessful();

        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'create_posts_table.php');
    }

    public function test_it_should_create_migration_file_with_table_option()
    {
        $this->artisan(
            command: 'module:make:migration',
            parameters: [
                'name' => 'add_title_to_posts_table',
                '--module' => $this->moduleName,
                '--table' => 'posts',
            ]
        )
            ->assertSuccessful();

        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'add_title_to_posts_table.php');
    }
}
