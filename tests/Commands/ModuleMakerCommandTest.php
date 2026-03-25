<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use Illuminate\Support\Facades\File;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeControllerCommand;
use NorbyBaru\Modularize\Tests\MakeCommandTestCase;
use ReflectionClass;

class ModuleMakerCommandTest extends MakeCommandTestCase
{
    public function test_it_returns_empty_array_when_no_modules_exist()
    {
        $this->cleanUp();

        $command = $this->app->make(ModuleMakeControllerCommand::class);
        $availableModules = $this->callProtectedMethod($command, 'getAvailableModules');

        $this->assertIsArray($availableModules);
        $this->assertEmpty($availableModules);
    }

    public function test_it_returns_available_modules()
    {
        // Create multiple test modules
        $modules = ['Blog', 'Shop', 'User'];

        foreach ($modules as $module) {
            $modulePath = $this->getModulePath($module);
            File::makeDirectory($modulePath, 0755, true);
        }

        $command = $this->app->make(ModuleMakeControllerCommand::class);
        $availableModules = $this->callProtectedMethod($command, 'getAvailableModules');

        $this->assertIsArray($availableModules);
        $this->assertCount(3, $availableModules);
        $this->assertContains('Blog', $availableModules);
        $this->assertContains('Shop', $availableModules);
        $this->assertContains('User', $availableModules);
    }

    public function test_it_returns_only_directories_as_modules()
    {
        // Create a test module directory
        $modulePath = $this->getModulePath($this->moduleName);
        File::makeDirectory($modulePath, 0755, true);

        // Create a file in the root module path (should not be returned)
        $rootPath = base_path(config('modularize.root_path'));
        File::put("{$rootPath}/test-file.txt", 'test content');

        $command = $this->app->make(ModuleMakeControllerCommand::class);
        $availableModules = $this->callProtectedMethod($command, 'getAvailableModules');

        $this->assertIsArray($availableModules);
        $this->assertCount(1, $availableModules);
        $this->assertContains($this->moduleName, $availableModules);
        $this->assertNotContains('test-file.txt', $availableModules);

        // Clean up
        File::delete("{$rootPath}/test-file.txt");
    }

    /**
     * Call a protected method on an object using reflection.
     *
     * @param  object  $object
     * @return mixed
     */
    protected function callProtectedMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
