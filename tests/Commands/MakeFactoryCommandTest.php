<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeFactoryCommandTest extends MakeCommandTestCase
{
    public function test_it_should_create_factory_file_with_model_name()
    {
        $this->artisan(
            command: 'module:make:factory',
            parameters: [
                'name' => 'Post',
                '--module' => $this->moduleName,
                '--model' => 'Post',
            ]
        )
            ->assertSuccessful();

        $this->assertFactoryFile(module: $this->moduleName, filename: 'PostFactory');
    }

    public function test_it_should_create_factory_file()
    {
        $this->artisan(
            command: 'module:make:factory',
            parameters: [
                'name' => 'Listing',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertFactoryFile(module: $this->moduleName, filename: 'ListingFactory');
    }
}
