<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ModuleCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularize:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster module discovery';

    /** @var Filesystem */
    protected $files;

    protected string $moduleRootPath;

    protected string $rootNamespace = 'Modules\\';

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->moduleRootPath = base_path(config('modularize.root_path'));

        if (! is_dir($this->moduleRootPath)) {
            $this->components->error('Modules directory does not exist: '.config('modularize.root_path'));

            return self::FAILURE;
        }

        $this->components->info('Building module cache...');

        $manifest = $this->buildModuleManifest();

        $cachePath = $this->getCachedModulesPath();
        $cacheDirectory = dirname($cachePath);

        // Ensure cache directory exists
        if (! is_dir($cacheDirectory)) {
            $this->files->makeDirectory($cacheDirectory, 0755, true);
        }

        // Write cache file
        $content = '<?php return '.var_export($manifest, true).';';
        $this->files->put($cachePath, $content);

        $this->components->info('Module cache created successfully.');
        $this->components->twoColumnDetail('Cache file', $cachePath);
        $this->components->twoColumnDetail('Modules cached', (string) count($manifest['modules']));

        return self::SUCCESS;
    }

    /**
     * Get the path to the cached modules file.
     */
    protected function getCachedModulesPath(): string
    {
        return base_path(config('modularize.cache_path', 'bootstrap/cache/modularize.php'));
    }

    /**
     * Build the module discovery manifest array.
     *
     * This method scans the filesystem to discover all modules and their associated
     * resources (service providers, configs, routes, etc.). The result will be cached
     * to avoid repeated filesystem scans on every request.
     */
    protected function buildModuleManifest(): array
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

        if (! is_dir($this->moduleRootPath)) {
            return $manifest;
        }

        $modules = array_map(
            'class_basename',
            $this->files->directories($this->moduleRootPath)
        );

        $manifest['modules'] = $modules;

        foreach ($modules as $module) {
            // Check for service provider
            $provider = "{$module}/Providers/{$module}ServiceProvider.php";
            $providerFile = "{$this->moduleRootPath}/$provider";
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
            $configFile = "{$this->moduleRootPath}/{$module}/config.php";
            if ($this->files->exists($configFile)) {
                $manifest['configs'][$module] = Str::slug($module);
            }

            // Check for console commands
            $consolePath = "{$this->moduleRootPath}/{$module}/Console";
            if (is_dir($consolePath)) {
                $commands = [];
                foreach ((new Finder)->in($consolePath)->files() as $command) {
                    $commandClass = $this->rootNamespace.str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($command->getRealPath(), realpath($this->moduleRootPath).DIRECTORY_SEPARATOR)
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
                    "{$this->moduleRootPath}/{$module}/routes.php",
                    "{$this->moduleRootPath}/{$module}/Routes/web.php",
                    "{$this->moduleRootPath}/{$module}/Routes/api.php",
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
            $helperFile = "{$this->moduleRootPath}/{$module}/helper.php";
            if ($this->files->exists($helperFile)) {
                $manifest['helpers'][$module] = $helperFile;
            }

            // Check for views directory
            $viewsPath = "{$this->moduleRootPath}/{$module}/Views";
            if ($this->files->isDirectory($viewsPath)) {
                $manifest['views'][$module] = [
                    'path' => $viewsPath,
                    'namespace' => $this->getModuleNamespace($module),
                ];
            }

            // Check for translations directory
            $translationsPath = "{$this->moduleRootPath}/{$module}/Lang";
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

    /**
     * Get the module namespace for views and translations.
     */
    private function getModuleNamespace(string $name): string
    {
        return Str::of($name)
            ->replace(search: '/', replace: '.')
            ->snake('-')
            ->replace(search: '.-', replace: '.')
            ->lower();
    }
}
