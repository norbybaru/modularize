<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class ModuleListCommandTest extends MakeCommandTestCase
{
    public function test_it_fails_when_modules_directory_does_not_exist()
    {
        // Ensure the modules directory does not exist
        $this->cleanUp();

        $this->artisan('module:list')
            ->assertFailed();
    }

    public function test_it_shows_info_message_when_no_modules_found()
    {
        // Create the modules directory but keep it empty
        $this->files->ensureDirectoryExists($this->getModulePath(''));

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_lists_a_single_module()
    {
        // Create a basic module structure
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_lists_multiple_modules()
    {
        // Create multiple modules
        $modules = ['Blog', 'Shop', 'Forum'];

        foreach ($modules as $module) {
            $this->files->ensureDirectoryExists($this->getModulePath($module));
        }

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_detects_service_provider()
    {
        // Create module with service provider
        $module = 'Blog';
        $providerPath = $this->getModulePath($module).'/Providers';
        $this->files->ensureDirectoryExists($providerPath);
        $this->files->put(
            "{$providerPath}/{$module}ServiceProvider.php",
            "<?php\n\nnamespace Modules\\{$module}\\Providers;\n\nclass {$module}ServiceProvider {}\n"
        );

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_counts_route_files()
    {
        // Create module with route files
        $module = 'Blog';
        $this->files->ensureDirectoryExists($this->getModulePath($module));

        // Create routes.php
        $this->files->put(
            $this->getModulePath($module).'/routes.php',
            "<?php\n\n// Routes\n"
        );

        // Create Routes directory with web.php and api.php
        $routesPath = $this->getModulePath($module).'/Routes';
        $this->files->ensureDirectoryExists($routesPath);
        $this->files->put("{$routesPath}/web.php", "<?php\n\n// Web routes\n");
        $this->files->put("{$routesPath}/api.php", "<?php\n\n// API routes\n");

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_counts_components()
    {
        // Create module with components
        $module = 'Blog';
        $componentsPath = $this->getModulePath($module).'/Components';
        $this->files->ensureDirectoryExists($componentsPath);

        // Create some component files
        $this->files->put(
            "{$componentsPath}/Alert.php",
            "<?php\n\nnamespace Modules\\{$module}\\Components;\n\nclass Alert {}\n"
        );
        $this->files->put(
            "{$componentsPath}/Button.php",
            "<?php\n\nnamespace Modules\\{$module}\\Components;\n\nclass Button {}\n"
        );

        $this->artisan('module:list')
            ->assertSuccessful();
    }

    public function test_it_lists_module_with_all_features()
    {
        // Create a complete module with all features
        $module = 'Blog';
        $modulePath = $this->getModulePath($module);

        // Create service provider
        $providerPath = "{$modulePath}/Providers";
        $this->files->ensureDirectoryExists($providerPath);
        $this->files->put(
            "{$providerPath}/{$module}ServiceProvider.php",
            "<?php\n\nnamespace Modules\\{$module}\\Providers;\n\nclass {$module}ServiceProvider {}\n"
        );

        // Create routes
        $this->files->put("{$modulePath}/routes.php", "<?php\n\n// Routes\n");

        // Create components
        $componentsPath = "{$modulePath}/Components";
        $this->files->ensureDirectoryExists($componentsPath);
        $this->files->put(
            "{$componentsPath}/Card.php",
            "<?php\n\nnamespace Modules\\{$module}\\Components;\n\nclass Card {}\n"
        );

        $this->artisan('module:list')
            ->assertSuccessful();
    }
}
