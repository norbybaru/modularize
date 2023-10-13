<?php

namespace NorbyBaru\Modularize;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMiddlewareCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeControllerCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeMigrationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeModelCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeNotificationCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakePolicyCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeProviderCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeRequestCommand;
use NorbyBaru\Modularize\Console\Commands\ModuleMakeResourceCommand;

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

        if (is_dir(app_path().'/Modules/')) {
            $modules = config('modules.enable')
                ?: array_map(
                    'class_basename',
                    $this->files->directories(app_path().'/Modules/')
                );

            foreach ($modules as $key => $module) {
                if (! $this->files->exists(app_path().'/Modules/'.$module.'/Controllers')) {
                    unset($modules[$key]);

                    $directories = array_map(
                        'class_basename',
                        $this->files->directories(app_path().'/Modules/'.$module)
                    );

                    foreach ($directories as $directory) {
                        array_push($modules, $module.'/'.$directory);
                    }
                }
            }

            foreach ($modules as $module) {
                $this->autoloadRoutes($module);
                $this->autoloadHelper($module);
                $this->autoloadViews($module);
                $this->autoloadTranslations($module);
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
        if ($this->app->runningInConsole()) {
            $this->registerMakeCommand();
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

    private function autoloadHelper(string $module): void
    {
        $helper = app_path().'/Modules/'.$module.'/helper.php';

        if ($this->files->exists($helper)) {
            include_once $helper;
        }
    }

    private function autoloadRoutes(string $module): void
    {
        if (! $this->app->routesAreCached()) {
            $route_files = [
                app_path().'/Modules/'.$module.'/routes.php',
                app_path().'/Modules/'.$module.'/routes/web.php',
                app_path().'/Modules/'.$module.'/routes/api.php',
            ];

            foreach ($route_files as $route_file) {
                if ($this->files->exists($route_file)) {
                    include $route_file;
                }
            }
        }
    }

    private function autoloadViews(string $module): void
    {
        $path = app_path().'/Modules/'.$module.'/Views';

        if ($this->files->isDirectory(directory: $path)) {
            $this->loadViewsFrom(
                path: $path,
                namespace: $this->getModuleNamespace(name: $module),
            );
        }
    }

    private function autoloadTranslations(string $module): void
    {
        $path = app_path().'/Modules/'.$module.'/Translations';

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
    //                 foreach ((array) $seed_list as $path) {
    //                     $seeder->call($seed_list);
    //                     // here goes the code that will print out in console that the migration was succesful
    //                 }
    //             });
    // }

    /**
     * Register module" console command.
     */
    protected function registerMakeCommand()
    {
        $this->commands([
            ModuleCommand::class,
            ModuleMakeControllerCommand::class,
            ModuleMakeModelCommand::class,
            ModuleMakeMiddlewareCommand::class,
            ModuleMakeMigrationCommand::class,
            ModuleMakeNotificationCommand::class,
            ModuleMakeProviderCommand::class,
            ModuleMakePolicyCommand::class,
            ModuleMakeResourceCommand::class,
            ModuleMakeRequestCommand::class,
        ]);
    }
}
