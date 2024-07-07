<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class MakeControllerCommandTest extends MakeCommandTestCase
{
    public function test_it_create_controller()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/PostController.php');
    }

    public function test_it_should_create_api_controller()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
                '--api' => true,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/PostController.php');

        $controller = $this->files->get(path: $this->getModulePath().'/Controllers/PostController.php');

        $methods = $this->getGeneratedClassMethods($controller);

        $this->assertStringContainsString(needle: 'function index()', haystack: $methods[0]);
        $this->assertStringContainsString(needle: 'function show(string $id)', haystack: $methods[1]);
        $this->assertStringContainsString(needle: 'function store(Request $request)', haystack: $methods[2]);
        $this->assertStringContainsString(needle: 'function update(Request $request, $id)', haystack: $methods[3]);
        $this->assertStringContainsString(needle: 'function destroy($id)', haystack: $methods[4]);
    }

    public function test_it_should_create_invokable_controller()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
                '--invokable' => true,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/PostController.php');

        $methods = $this->getGeneratedClassMethods(
            subjectFile: $this->files
                ->get(path: $this->getModulePath().'/Controllers/PostController.php')
        );

        $this->assertStringContainsString(needle: 'function __invoke(Request $request)', haystack: $methods[0]);
    }

    public function test_it_should_create_resourceful_controller()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
                '--resource' => true,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/PostController.php');

        $methods = $this->getGeneratedClassMethods(
            subjectFile: $this->files
                ->get(path: $this->getModulePath().'/Controllers/PostController.php')
        );

        $this->assertStringContainsString(needle: 'function index()', haystack: $methods[0]);
        $this->assertStringContainsString(needle: 'function create()', haystack: $methods[1]);
        $this->assertStringContainsString(needle: 'function store(Request $request)', haystack: $methods[2]);
        $this->assertStringContainsString(needle: 'function show($id)', haystack: $methods[3]);
        $this->assertStringContainsString(needle: 'function edit($id)', haystack: $methods[4]);
        $this->assertStringContainsString(needle: 'function update(Request $request, $id)', haystack: $methods[5]);
        $this->assertStringContainsString(needle: 'function destroy($id)', haystack: $methods[6]);
    }

    public function test_it_should_create_controller_in_sub_directory()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'Api/CreatePostController',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/Api/CreatePostController.php');
    }

    public function test_it_should_not_create_controller_with_duplicate_name()
    {
        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
            ]
        )
            ->assertSuccessful();

        $this->assertFileExists(filename: $this->getModulePath().'/Controllers/PostController.php');

        $this->artisan(
            command: 'module:make:controller',
            parameters: [
                'name' => 'PostController',
                '--module' => $this->moduleName,
            ]
        )
            ->assertFailed();
    }
}
