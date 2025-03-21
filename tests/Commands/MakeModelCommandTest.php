<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeModelCommandTest extends MakeCommandTestCase
{
    public function test_it_creates_a_model()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
    }

    public function test_it_creates_a_model_with_migration()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Video',
                '--module' => $this->moduleName,
                '--migration' => true,
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Video.php');

        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'create_videos_table.php');
    }

    public function test_it_creates_a_model_with_factory()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--factory' => true,
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
        $this->assertFactoryFile(module: $this->moduleName, filename: 'PostFactory');
    }

    public function test_it_creates_a_model_with_migration_and_factory()
    {
        $module = 'Search';
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Listing',
                '--module' => $module,
                '--migration' => true,
                '--factory' => true,
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertMigrationFile(module: $module, migrationFilename: 'create_listings_table.php');

        $this->assertFileExists(
            filename: $this->getModulePath($module).'/Models/Listing.php'
        );

        $this->assertFactoryFile(module: $module, filename: 'ListingFactory');
    }

    public function test_it_creates_a_model_with_migration_and_factory_and_test()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--migration' => true,
                '--factory' => true,
                // '--test' => true
            ])
            ->assertExitCode(exitCode: 0);

    }
}
