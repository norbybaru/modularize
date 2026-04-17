<?php

namespace NorbyBaru\Modularize\Tests;

use Illuminate\Support\Facades\File;

class CacheCommandTest extends MakeCommandTestCase
{
    protected function getCachePath(): string
    {
        return base_path(config('modularize.cache_path', 'bootstrap/cache/modularize.php'));
    }

    protected function tearDown(): void
    {
        // Clean up cache file after each test
        $cachePath = $this->getCachePath();
        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }

        parent::tearDown();
    }

    public function test_it_creates_cache_file_successfully()
    {
        // Create a basic module structure
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $this->assertFileExists($cachePath);

        // Verify cache content is valid PHP
        $cachedData = require $cachePath;
        $this->assertIsArray($cachedData);
        $this->assertArrayHasKey('modules', $cachedData);
        $this->assertArrayHasKey('service_providers', $cachedData);
        $this->assertArrayHasKey('configs', $cachedData);
        $this->assertArrayHasKey('console_commands', $cachedData);
        $this->assertArrayHasKey('routes', $cachedData);
        $this->assertArrayHasKey('helpers', $cachedData);
        $this->assertArrayHasKey('views', $cachedData);
        $this->assertArrayHasKey('translations', $cachedData);
        $this->assertArrayHasKey('view_components', $cachedData);
    }

    public function test_it_fails_when_modules_directory_does_not_exist()
    {
        // Ensure the modules directory does not exist
        $this->cleanUp();

        $this->artisan('modularize:cache')
            ->assertFailed();

        // Cache file should not be created
        $cachePath = $this->getCachePath();
        $this->assertFileDoesNotExist($cachePath);
    }

    public function test_it_creates_cache_directory_if_not_exists()
    {
        // Create module structure
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        // Ensure cache directory doesn't exist
        $cacheDirectory = dirname($this->getCachePath());
        if ($this->files->exists($cacheDirectory)) {
            $this->files->deleteDirectory($cacheDirectory);
        }

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $this->assertFileExists($cachePath);
        $this->assertDirectoryExists($cacheDirectory);
    }

    public function test_it_caches_single_module()
    {
        // Create a basic module structure
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertCount(1, $cachedData['modules']);
        $this->assertContains($this->moduleName, $cachedData['modules']);
    }

    public function test_it_caches_multiple_modules()
    {
        // Create multiple modules
        $modules = ['Blog', 'Shop', 'Forum'];

        foreach ($modules as $module) {
            $this->files->ensureDirectoryExists($this->getModulePath($module));
        }

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertCount(3, $cachedData['modules']);
        foreach ($modules as $module) {
            $this->assertContains($module, $cachedData['modules']);
        }
    }

    public function test_it_caches_service_provider()
    {
        $module = 'Blog';
        $providerPath = $this->getModulePath($module).'/Providers';
        $this->files->ensureDirectoryExists($providerPath);

        // Create a valid service provider
        $providerContent = <<<PHP
<?php

namespace Modules\\{$module}\\Providers;

use Illuminate\Support\ServiceProvider;

class {$module}ServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }
}
PHP;

        $this->files->put("{$providerPath}/{$module}ServiceProvider.php", $providerContent);

        // Require the file so the class is available for reflection
        require_once "{$providerPath}/{$module}ServiceProvider.php";

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['service_providers']);
        $this->assertEquals("Modules\\{$module}\\Providers\\{$module}ServiceProvider", $cachedData['service_providers'][$module]);
    }

    public function test_it_caches_config_file()
    {
        $module = 'Blog';
        $this->files->ensureDirectoryExists($this->getModulePath($module));

        // Create config file
        $this->files->put(
            $this->getModulePath($module).'/config.php',
            "<?php\n\nreturn [];\n"
        );

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['configs']);
    }

    public function test_it_caches_routes()
    {
        $module = 'Blog';
        $this->files->ensureDirectoryExists($this->getModulePath($module));

        // Create routes.php
        $this->files->put(
            $this->getModulePath($module).'/routes.php',
            "<?php\n\n// Routes\n"
        );

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['routes']);
        $this->assertNotEmpty($cachedData['routes'][$module]);
    }

    public function test_it_caches_helper_file()
    {
        $module = 'Blog';
        $this->files->ensureDirectoryExists($this->getModulePath($module));

        // Create helper file
        $this->files->put(
            $this->getModulePath($module).'/helper.php',
            "<?php\n\n// Helper functions\n"
        );

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['helpers']);
    }

    public function test_it_caches_views_directory()
    {
        $module = 'Blog';
        $viewsPath = $this->getModulePath($module).'/Views';
        $this->files->ensureDirectoryExists($viewsPath);

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['views']);
        $this->assertArrayHasKey('path', $cachedData['views'][$module]);
        $this->assertArrayHasKey('namespace', $cachedData['views'][$module]);
    }

    public function test_it_caches_translations_directory()
    {
        $module = 'Blog';
        $translationsPath = $this->getModulePath($module).'/Lang';
        $this->files->ensureDirectoryExists($translationsPath);

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        $this->assertArrayHasKey($module, $cachedData['translations']);
        $this->assertArrayHasKey('path', $cachedData['translations'][$module]);
        $this->assertArrayHasKey('namespace', $cachedData['translations'][$module]);
    }

    public function test_it_overwrites_existing_cache()
    {
        // Create initial module
        $this->files->ensureDirectoryExists($this->getModulePath('Blog'));

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $firstCachedData = require $cachePath;
        $this->assertCount(1, $firstCachedData['modules']);

        // Add another module and re-cache
        $this->files->ensureDirectoryExists($this->getModulePath('Shop'));

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $secondCachedData = require $cachePath;
        $this->assertCount(2, $secondCachedData['modules']);
    }

    public function test_clear_command_removes_cache_file()
    {
        // Create cache first
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $this->assertFileExists($cachePath);

        // Clear cache
        $this->artisan('modularize:clear')
            ->assertSuccessful();

        $this->assertFileDoesNotExist($cachePath);
    }

    public function test_clear_command_succeeds_when_cache_does_not_exist()
    {
        $cachePath = $this->getCachePath();

        // Ensure cache doesn't exist
        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }

        $this->artisan('modularize:clear')
            ->assertSuccessful();
    }

    public function test_it_caches_module_with_all_features()
    {
        // Create a complete module with all features
        $module = 'Blog';
        $modulePath = $this->getModulePath($module);

        // Create service provider
        $providerPath = "{$modulePath}/Providers";
        $this->files->ensureDirectoryExists($providerPath);
        $providerContent = <<<PHP
<?php

namespace Modules\\{$module}\\Providers;

use Illuminate\Support\ServiceProvider;

class {$module}ServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }
}
PHP;
        $this->files->put("{$providerPath}/{$module}ServiceProvider.php", $providerContent);

        // Require the file so the class is available for reflection
        require_once "{$providerPath}/{$module}ServiceProvider.php";

        // Create config
        $this->files->put("{$modulePath}/config.php", "<?php\n\nreturn [];\n");

        // Create routes
        $this->files->put("{$modulePath}/routes.php", "<?php\n\n// Routes\n");

        // Create helper
        $this->files->put("{$modulePath}/helper.php", "<?php\n\n// Helpers\n");

        // Create views directory
        $this->files->ensureDirectoryExists("{$modulePath}/Views");

        // Create translations directory
        $this->files->ensureDirectoryExists("{$modulePath}/Lang");

        $this->artisan('modularize:cache')
            ->assertSuccessful();

        $cachePath = $this->getCachePath();
        $cachedData = require $cachePath;

        // Verify all features are cached
        $this->assertContains($module, $cachedData['modules']);
        $this->assertArrayHasKey($module, $cachedData['service_providers']);
        $this->assertArrayHasKey($module, $cachedData['configs']);
        $this->assertArrayHasKey($module, $cachedData['routes']);
        $this->assertArrayHasKey($module, $cachedData['helpers']);
        $this->assertArrayHasKey($module, $cachedData['views']);
        $this->assertArrayHasKey($module, $cachedData['translations']);
        $this->assertArrayHasKey($module, $cachedData['view_components']);
    }
}
