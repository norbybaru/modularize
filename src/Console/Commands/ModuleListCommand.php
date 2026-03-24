<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ModuleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all existing modules in the project';

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

        $modules = $this->getModules();

        if (empty($modules)) {
            $this->components->info('No modules found.');

            return self::SUCCESS;
        }

        $this->displayModulesTable($modules);

        return self::SUCCESS;
    }

    /**
     * Get all modules with their details.
     */
    protected function getModules(): array
    {
        $moduleDirectories = array_map(
            'class_basename',
            $this->files->directories($this->moduleRootPath)
        );

        $modules = [];

        foreach ($moduleDirectories as $module) {
            $modules[] = [
                'name' => $module,
                'path' => config('modularize.root_path').'/'.$module,
                'service_provider' => $this->hasServiceProvider($module) ? '✓' : '✗',
                'routes' => $this->countRouteFiles($module),
                'components' => $this->countComponents($module),
            ];
        }

        return $modules;
    }

    /**
     * Check if module has a service provider.
     */
    protected function hasServiceProvider(string $module): bool
    {
        $provider = "{$module}/Providers/{$module}ServiceProvider.php";
        $file = "{$this->moduleRootPath}/$provider";

        return $this->files->exists($file);
    }

    /**
     * Count route files in the module.
     */
    protected function countRouteFiles(string $module): int
    {
        $path = "{$this->moduleRootPath}/{$module}";
        $count = 0;

        $routeFiles = [
            $path.'/routes.php',
            $path.'/Routes/web.php',
            $path.'/Routes/api.php',
        ];

        foreach ($routeFiles as $routePath) {
            if ($this->files->isDirectory($routePath)) {
                $count += count($this->files->allFiles($routePath));
            } elseif ($this->files->exists($routePath)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count components in the module.
     */
    protected function countComponents(string $module): int
    {
        $path = "{$this->moduleRootPath}/{$module}/Components";

        if (! $this->files->isDirectory($path)) {
            return 0;
        }

        try {
            $files = (new Finder)->in($path)->files()->name('*.php');

            return iterator_count($files);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Display modules in a table format.
     */
    protected function displayModulesTable(array $modules): void
    {
        $this->components->twoColumnDetail('<fg=gray>Module</>', '<fg=gray>Details</>');
        $this->newLine();

        foreach ($modules as $module) {
            $this->components->twoColumnDetail(
                '<fg=green>'.$module['name'].'</>',
                'Path: '.$module['path']
            );
            $this->components->twoColumnDetail(
                '  Service Provider',
                $module['service_provider']
            );
            $this->components->twoColumnDetail(
                '  Routes',
                (string) $module['routes']
            );
            $this->components->twoColumnDetail(
                '  Components',
                (string) $module['components']
            );
            $this->newLine();
        }

        $this->components->info('Total modules: '.count($modules));
    }
}
