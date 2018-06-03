<?php namespace norbybaru\modularize;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use NorbyBaru\Modularize\Console\Commands\ModuleCommand;

/**
 * Class ModularizeServiceProvider
 *
 * Bootstrap your app with modular classes
 *
 * @author Norby Baruani <norbybaru@gmail.com>
 * @package norbybaru\modularize
 * @version 1.2.1
 * @since 1.0.0
 */
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
            $modules = config("modules.enable")
                ?: array_map(
                    'class_basename',
                    $this->files->directories(app_path().'/Modules/')
                );

            foreach ($modules as $key => $module) {
                if (!$this->files->exists(app_path() . '/Modules/' . $module . '/Controllers')) {
                    unset($modules[$key]);

                    $directories = array_map(
                        'class_basename',
                        $this->files->directories(app_path().'/Modules/' . $module)
                    );

                    foreach ($directories as $directory) {
                        array_push($modules, $module . '/' . $directory);
                    }
                }
            }

            foreach ($modules as $module) {
                // Allow routes to be cached
                if (!$this->app->routesAreCached()) {
                    $route_files = [
                        app_path() . '/Modules/' . $module . '/routes.php',
                        app_path() . '/Modules/' . $module . '/routes/web.php',
                        app_path() . '/Modules/' . $module . '/routes/api.php',
                    ];

                    foreach ($route_files as $route_file) {
                        if ($this->files->exists($route_file)) {
                            include $route_file;
                        }
                    }
                }

                $helper = app_path() . '/Modules/' . $module . '/helper.php';
                $views  = app_path() . '/Modules/' . $module . '/Views';
                $trans  = app_path() . '/Modules/' . $module . '/Translations';

                if ($this->files->exists($helper)) {
                    include_once $helper;
                }

                if ($this->files->isDirectory($views)) {
                    $this->loadViewsFrom($views, strtolower(str_replace('/', '.', $module)));
                }

                if ($this->files->isDirectory($trans)) {
                    $this->loadTranslationsFrom($trans, strtolower(str_replace('/', '.', $module)));
                }
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
        $this->registerMakeCommand();
    }

    /**
     * Register module" console command.
     *
     */
    protected function registerMakeCommand()
    {
        $this->commands('modules.make');

        $bind_method = method_exists($this->app, 'bindShared') ? 'bindShared' : 'singleton';
        $this->app->{$bind_method}('modules.make', function () {
            return new ModuleCommand($this->files);
        });
    }
}
