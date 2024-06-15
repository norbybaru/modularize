<?php

namespace NorbyBaru\Modularize;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeComponentCommand;
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

class ModularizeServiceProvider extends ServiceProvider
{
    /** @var Filesystem */
    protected $files;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        $moduleRootPath = base_path(config('modularize.root_path'));
        //$moduleRootPath  = app_path('/Modules');

        //dd($moduleRootPath, app_path());
        if (is_dir($moduleRootPath)) {

            if (! config('modularize.enable')) {
                return;
            }

            $modules = array_map(
                'class_basename',
                $this->files->directories($moduleRootPath)
            );

            // foreach ($modules as $key => $module) {
            //     if (! $this->files->exists("{$moduleRootPath}/{$module}/Controllers")) {
            //         unset($modules[$key]);

            //         $directories = array_map(
            //             'class_basename',
            //             $this->files->directories($moduleRootPath.'/'.$module)
            //         );

            //         foreach ($directories as $directory) {
            //             array_push($modules, $module.'/'.$directory);
            //         }
            //     }
            // }

            foreach ($modules as $module) {
                $this->autoloadServiceProvider($moduleRootPath, $module);
                $this->autoloadConfig($moduleRootPath, $module);
                $this->autoloadMigration($moduleRootPath, $module);
                $this->autoloadRoutes($moduleRootPath, $module);
                $this->autoloadHelper($moduleRootPath, $module);
                $this->autoloadViews($moduleRootPath, $module);
                $this->autoloadTranslations($moduleRootPath, $module);
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
        $provider = "{$moduleRootPath}/{$module}/Providers/{$module}ServiceProvider.php";

        if ($this->files->exists($provider)) {
            $this->app->register($provider);
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
                $path.'/Routes/',
            ];

            foreach ($routeFiles as $path) {
                if ($this->files->isDirectory(directory: $path)) {
                    foreach ($this->files->files(directory: $path) as $file) {
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
