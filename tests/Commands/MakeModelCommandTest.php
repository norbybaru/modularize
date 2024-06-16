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
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--migration' => true,
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
        $this->assertFileExists(filename: $this->getModulePath().'/Database/migration/'.date('Y_m_d_His').'_create_posts_table.php');
    }

    public function test_it_creates_a_model_with_factory()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                //'--factory' => true
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertFileExists(filename: $this->getModulePath().'/Models/Post.php');
        //$this->assertFileExists($this->getModulePath($this->moduleName).'/Database/Factories/PostFactory.php');
    }

    public function test_it_creates_a_model_with_migration_and_factory()
    {
        $this->artisan(
            command: 'module:make:model',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--migration' => true,
                //'--factory' => true
            ]
        )
            ->assertExitCode(exitCode: 0);

        $this->assertFileExists(
            filename: $this->getModulePath().'/Models/Post.php'
        );
        $this->assertFileExists(
            filename: $this->getModulePath().'/Database/migration/'.date('Y_m_d_His').'_create_posts_table.php'
        );
        //$this->assertFileExists($this->getModulePath($this->moduleName).'/Database/factories/PostFactory.php');
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
                //'--test' => true
            ])
            ->assertExitCode(exitCode: 0);

    }
}
