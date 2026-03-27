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

    public function test_it_creates_a_model_with_seeder()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--seed' => true,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
        $this->assertSeederFile(module: $this->moduleName, filename: 'PostSeeder');
    }

    public function test_it_creates_a_model_with_all_option()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Article',
                '--module' => $this->moduleName,
                '--all' => true,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Article.php');
        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'create_articles_table.php');
        $this->assertFactoryFile(module: $this->moduleName, filename: 'ArticleFactory');
        $this->assertSeederFile(module: $this->moduleName, filename: 'ArticleSeeder');
    }

    public function test_it_should_fail_to_create_model_on_duplicate_filename()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');

        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
            ]
        )->assertFailed();

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
    }

    public function test_it_displays_improved_error_message_when_file_already_exists()
    {
        // Create the model first time - should succeed
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'User',
                '--module' => $this->moduleName,
            ]
        )->assertSuccessful();

        $filePath = $this->getModulePath().'/Models/User.php';
        $this->assertFileExists(filename: $filePath);

        // Try to create the same model again - should fail with improved error message
        // The error message format is: "Model [<full-path>] already exists." (see ModuleMakerCommand::logFileExist)
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'User',
                '--module' => $this->moduleName,
            ]
        )
            ->expectsOutputToContain('already exists')  // Verify correct grammar (not "already exist")
            ->expectsOutputToContain('--force')         // Verify --force suggestion is shown
            ->assertFailed();
    }

    public function test_it_displays_summary_table_with_all_option()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Product',
                '--module' => $this->moduleName,
                '--all' => true,
            ]
        )
            ->expectsOutputToContain('Generated Files')
            ->expectsOutputToContain('Total files generated:')
            ->assertSuccessful();

        // Verify files were actually created
        $this->assertFileExists(filename: $this->getModulePath().'/Models/Product.php');
        $this->assertMigrationFile(module: $this->moduleName, migrationFilename: 'create_products_table.php');
        $this->assertFactoryFile(module: $this->moduleName, filename: 'ProductFactory');
        $this->assertSeederFile(module: $this->moduleName, filename: 'ProductSeeder');
    }
}
