<?php

namespace NorbyBaru\Modularize;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeComponentCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeConsoleCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeControllerCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeEventCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeJobCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeListenerCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMiddlewareCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMigrationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeModelCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeNotificationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakePolicyCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeProviderCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeRequestCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeResourceCommand;
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
            ModuleMakeComponentCommand::class,
            ModuleMakeConsoleCommand::class,
            ModuleMakeControllerCommand::class,
            ModuleMakeEventCommand::class,
            ModuleMakeJobCommand::class,
            ModuleMakeListenerCommand::class,
            ModuleMakeModelCommand::class,
            ModuleMakeMiddlewareCommand::class,
            ModuleMakeMigrationCommand::class,
            ModuleMakeNotificationCommand::class,
            ModuleMakeProviderCommand::class,
            ModuleMakePolicyCommand::class,
            ModuleMakeResourceCommand::class,
            ModuleMakeRequestCommand::class,
            ModuleMakeTestCommand::class,
            ModuleMakeViewCommand::class,
        ]);
    }
}
