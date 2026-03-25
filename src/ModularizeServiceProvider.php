<?php

namespace NorbyBaru\Modularize;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use NorbyBaru\Modularize\Console\Commands\ModuleCacheCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleClearCacheCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleListCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeComponentCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeConsoleCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeControllerCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeEventCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeFactoryCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeJobCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeListenerCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMailCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMiddlewareCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMigrationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeModelCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeNotificationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakePolicyCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeProviderCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeRequestCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeResourceCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeSeederCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeTestCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeViewCommand;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ModularizeServiceProvider extends ServiceProvider
{
    /** @var Filesystem */
    protected $files;

    protected string $moduleRootPath;

    protected string $rootNamespace = 'Modules\\';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        if (is_dir($this->moduleRootPath = base_path(config('modularize.root_path')))) {
            if (! config('modularize.enable')) {
                return;
            }

            $modules = array_map(
                'class_basename',
                $this->files->directories($this->moduleRootPath)
            );

            foreach ($modules as $module) {
                $this->autoloadServiceProvider($this->moduleRootPath, $module);
                $this->autoloadConfig($this->moduleRootPath, $module);
                $this->autoloadConsoleCommands($this->moduleRootPath, $module);
                $this->autoloadMigration($this->moduleRootPath, $module);
                $this->autoloadRoutes($this->moduleRootPath, $module);
                $this->autoloadHelper($this->moduleRootPath, $module);
                $this->autoloadViews($this->moduleRootPath, $module);
                $this->autoloadTranslations($this->moduleRootPath, $module);
                $this->autoloadViewComponents($module);
            }
        }

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = new Filesystem;

        $this->mergeConfigFrom($this->configPath(), 'modularize');

        if ($this->app->runningInConsole()) {
            $this->registerMakeCommand();
        }
    }

    /**
     * Return config file.
     *
     * @return string
     */
    protected function configPath()
    {
        return __DIR__.'/../config/modularize.php';
    }

    /**
     * Publish config file.
     */
    protected function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path('modularize.php'),
            ], 'modularize-config');
        }
    }

    /**
     * Get the path to the cached modules file.
     *
     * @return string
     */
    protected function getCachedModulesPath()
    {
        return base_path(config('modularize.cache_path', 'bootstrap/cache/modularize.php'));
    }

    /**
     * Determine if the module discovery results are cached.
     *
     * @return bool
     */
    protected function modulesAreCached()
    {
        return config('modularize.cache_enabled', false) && $this->files->exists($this->getCachedModulesPath());
    }

    /**
     * Build the module discovery manifest array.
     *
     * This method scans the filesystem to discover all modules and their associated
     * resources (service providers, configs, routes, etc.). The result can be cached
     * to avoid repeated filesystem scans on every request.
     *
     * @return array
     */
    protected function buildModuleManifest()
    {
        $manifest = [
            'modules' => [],
            'service_providers' => [],
            'configs' => [],
            'console_commands' => [],
            'routes' => [],
            'helpers' => [],
            'views' => [],
            'translations' => [],
            'view_components' => [],
        ];

        if (! is_dir($moduleRootPath = base_path(config('modularize.root_path')))) {
            return $manifest;
        }

        $modules = array_map(
            'class_basename',
            $this->files->directories($moduleRootPath)
        );

        $manifest['modules'] = $modules;

        foreach ($modules as $module) {
            // Check for service provider
            $provider = "{$module}/Providers/{$module}ServiceProvider.php";
            $providerFile = "{$moduleRootPath}/$provider";
            if ($this->files->exists($providerFile)) {
                $providerNamespace = $this->rootNamespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $provider
                );

                if (
                    is_subclass_of($providerNamespace, ServiceProvider::class)
                    && ! (new ReflectionClass($providerNamespace))->isAbstract()
                ) {
                    $manifest['service_providers'][$module] = $providerNamespace;
                }
            }

            // Check for config file
            $configFile = "{$moduleRootPath}/{$module}/config.php";
            if ($this->files->exists($configFile)) {
                $manifest['configs'][$module] = Str::slug($module);
            }

            // Check for console commands
            $consolePath = "{$moduleRootPath}/{$module}/Console";
            if (is_dir($consolePath)) {
                $commands = [];
                foreach ((new Finder)->in($consolePath)->files() as $command) {
                    $commandClass = $this->rootNamespace.str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($command->getRealPath(), realpath($moduleRootPath).DIRECTORY_SEPARATOR)
                    );

                    if (
                        is_subclass_of($commandClass, Command::class)
                        && ! (new ReflectionClass($commandClass))->isAbstract()
                    ) {
                        $commands[] = $commandClass;
                    }
                }

                if (! empty($commands)) {
                    $manifest['console_commands'][$module] = $commands;
                }
            }

            // Check for routes
            if (config('modularize.autoload_routes')) {
                $routeFiles = [
                    "{$moduleRootPath}/{$module}/routes.php",
                    "{$moduleRootPath}/{$module}/Routes/web.php",
                    "{$moduleRootPath}/{$module}/Routes/api.php",
                ];

                $existingRoutes = [];
                foreach ($routeFiles as $routeFile) {
                    if ($this->files->isDirectory($routeFile)) {
                        foreach ($this->files->allFiles($routeFile) as $file) {
                            $existingRoutes[] = $file->getPathname();
                        }
                    } elseif ($this->files->exists($routeFile)) {
                        $existingRoutes[] = $routeFile;
                    }
                }

                if (! empty($existingRoutes)) {
                    $manifest['routes'][$module] = $existingRoutes;
                }
            }

            // Check for helper file
            $helperFile = "{$moduleRootPath}/{$module}/helper.php";
            if ($this->files->exists($helperFile)) {
                $manifest['helpers'][$module] = $helperFile;
            }

            // Check for views directory
            $viewsPath = "{$moduleRootPath}/{$module}/Views";
            if ($this->files->isDirectory($viewsPath)) {
                $manifest['views'][$module] = [
                    'path' => $viewsPath,
                    'namespace' => $this->getModuleNamespace($module),
                ];
            }

            // Check for translations directory
            $translationsPath = "{$moduleRootPath}/{$module}/Lang";
            if ($this->files->isDirectory($translationsPath)) {
                $manifest['translations'][$module] = [
                    'path' => $translationsPath,
                    'namespace' => $this->getModuleNamespace($module),
                ];
            }

            // Store view component namespace
            $manifest['view_components'][$module] = [
                'namespace' => "Modules\\{$module}\\Components",
                'alias' => $this->getModuleNamespace($module),
            ];
        }

        return $manifest;
    }

    private function getModuleNamespace(string $name): string
    {
        return Str::of($name)
            ->replace(search: '/', replace: '.')
            ->snake('-')
            ->replace(search: '.-', replace: '.')
            ->lower();
    }

    /**
     * Load module migration files
     */
    private function autoloadMigration(string $moduleRootPath, string $module)
    {
        $this->loadMigrationsFrom("{$moduleRootPath}/{$module}/Database/migrations");
    }

    /**
     * Load module console commands
     */
    private function autoloadConsoleCommands(string $moduleRootPath, string $module): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $path = "{$moduleRootPath}/{$module}/Console";

        $paths = array_unique(Arr::wrap($path));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->rootNamespace;

        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getRealPath(), realpath($this->moduleRootPath).DIRECTORY_SEPARATOR)
            );

            if (
                is_subclass_of($command, Command::class)
                && ! (new ReflectionClass($command))->isAbstract()
            ) {
                $this->commands($command);
            }
        }
    }

    /**
     * Load module config.php file
     */
    private function autoloadConfig(string $moduleRootPath, string $module)
    {
        $config = "{$moduleRootPath}/{$module}/config.php";

        if ($this->files->exists($config)) {
            $this->mergeConfigFrom($config, Str::slug($module));
        }
    }

    /**
     * Load and register module service provider
     */
    private function autoloadServiceProvider(string $moduleRootPath, string $module)
    {
        $provider = "{$module}/Providers/{$module}ServiceProvider.php";
        $file = "{$moduleRootPath}/$provider";
        if ($this->files->exists($file)) {
            $providerNamespace = $this->rootNamespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                $provider
            );

            if (
                is_subclass_of($providerNamespace, ServiceProvider::class)
                && ! (new ReflectionClass($providerNamespace))->isAbstract()
            ) {
                $this->app->register($providerNamespace);
            }
        }
    }

    private function autoloadHelper(string $moduleRootPath, string $module): void
    {
        $path = "{$moduleRootPath}/$module/helper.php";

        if ($this->files->exists($path)) {
            include_once $path;
        }
    }

    private function autoloadRoutes(string $moduleRootPath, string $module): void
    {
        if (! config('modularize.autoload_routes')) {
            return;
        }

        $path = "{$moduleRootPath}/{$module}";
        if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
            $routeFiles = [
                $path.'/routes.php',
                $path.'/Routes/web.php',
                $path.'/Routes/api.php',
            ];

            foreach ($routeFiles as $path) {
                if ($this->files->isDirectory(directory: $path)) {
                    foreach ($this->files->allFiles(directory: $path) as $file) {
                        include $file->getPathname();
                    }
                } else {
                    if ($this->files->exists(path: $path)) {
                        include $path;
                    }
                }
            }
        }
    }

    private function autoloadViews(string $moduleRootPath, string $module): void
    {
        $path = "{$moduleRootPath}/{$module}/Views";

        if ($this->files->isDirectory(directory: $path)) {
            $this->loadViewsFrom(
                path: $path,
                namespace: $this->getModuleNamespace(name: $module),
            );
        }
    }

    private function autoloadViewComponents(string $module): void
    {
        Blade::componentNamespace(
            "Modules\\{$module}\\Components",
            $this->getModuleNamespace(name: $module)
        );
    }

    private function autoloadTranslations(string $moduleRootPath, string $module): void
    {
        $path = "{$moduleRootPath}/{$module}/Lang";

        if ($this->files->isDirectory(directory: $path)) {
            $this->loadTranslationsFrom(
                path: $path,
                namespace: $this->getModuleNamespace(name: $module),
            );
        }
    }

    // protected function loadSeeders($seed_list)
    // {
    //     $this->callAfterResolving(DatabaseSeeder::class, function ($seeder) use ($seed_list) {
    //         foreach ((array) $seed_list as $path) {
    //             $seeder->call($seed_list);
    //             // here goes the code that will print out in console that the migration was succesful
    //         }
    //     });
    // }

    /**
     * Register module" console command.
     */
    protected function registerMakeCommand()
    {
        $this->commands([
            ModuleCacheCommand::class,
            ModuleClearCacheCommand::class,
            ModuleMakeComponentCommand::class,
            ModuleMakeConsoleCommand::class,
            ModuleMakeControllerCommand::class,
            ModuleMakeEventCommand::class,
            ModuleMakeFactoryCommand::class,
            ModuleMakeJobCommand::class,
            ModuleListCommand::class,
            ModuleMakeListenerCommand::class,
            ModuleMakeMailCommand::class,
            ModuleMakeModelCommand::class,
            ModuleMakeMiddlewareCommand::class,
            ModuleMakeMigrationCommand::class,
            ModuleMakeNotificationCommand::class,
            ModuleMakeProviderCommand::class,
            ModuleMakePolicyCommand::class,
            ModuleMakeResourceCommand::class,
            ModuleMakeRequestCommand::class,
            ModuleMakeSeederCommand::class,
            ModuleMakeTestCommand::class,
            ModuleMakeViewCommand::class,
        ]);
    }
}
