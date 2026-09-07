<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use NorbyBaru\Modularize\ModularizeServiceProvider;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Finder\Finder;

class ModuleListCommand extends Command
{
    /**
     * Artifact directories reported under `-v`, in display order.
     *
     * Label => [path relative to the module, filename pattern]. Counted recursively, so
     * nested files such as Controllers/Api/UserController.php are included.
     *
     * Casing is load-bearing and mirrors each generator's getFolderPath(); note
     * `Database/migrations` is lowercase while every other directory is StudlyCase.
     *
     * @var array<string, array{string, string}>
     */
    private const ARTIFACT_DIRECTORIES = [
        'Console' => ['Console', '*.php'],
        'Controllers' => ['Controllers', '*.php'],
        'Middleware' => ['Middleware', '*.php'],
        'Requests' => ['Requests', '*.php'],
        'Resources' => ['Resources', '*.php'],
        'Models' => ['Models', '*.php'],
        'Policies' => ['Policies', '*.php'],
        'Migrations' => ['Database/migrations', '*.php'],
        'Factories' => ['Database/Factories', '*.php'],
        'Seeders' => ['Database/Seeders', '*.php'],
        'Events' => ['Events', '*.php'],
        'Listeners' => ['Listeners', '*.php'],
        'Jobs' => ['Jobs', '*.php'],
        'Mail' => ['Mail', '*.php'],
        'Notifications' => ['Notifications', '*.php'],
        // Counted alongside the separate Service Provider ✓/✗ row on purpose: that row
        // answers "is the conventional <Module>ServiceProvider there", this one answers
        // "how many providers does the module define".
        'Providers' => ['Providers', '*.php'],
        // Components and Views both count a generated component: make:component writes
        // Components/Alert.php (the class) and Views/Components/alert.blade.php (its view).
        // Two distinct files, each counted once, and two distinct runtime registrations.
        'Components' => ['Components', '*.php'],
        'Views' => ['Views', '*.blade.php'],
        'Lang' => ['Lang', '*.php'],
        'Tests' => ['Tests', '*.php'],
    ];

    /**
     * Single files reported under `-v` by presence rather than count.
     *
     * @var array<string, string>
     */
    private const ARTIFACT_FILES = [
        'Config' => 'config.php',
        'Helper' => 'helper.php',
    ];

    /**
     * Indent for artifact rows, nesting them under their module.
     *
     * Non-breaking spaces are deliberate: twoColumnDetail renders through Termwind, which
     * collapses ordinary leading whitespace inside its span.
     */
    private const DETAIL_INDENT = "\u{00A0}\u{00A0}";

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

        if ($this->output->isVerbose()) {
            $this->displayModuleDetails($modules);
        } else {
            $this->displayModulesTable($modules);
        }

        $this->components->info('Total modules: '.count($modules));

        return self::SUCCESS;
    }

    /**
     * Get all modules with their name and path.
     *
     * @return array<int, array{name: string, path: string}>
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

        return $this->files->exists("{$this->moduleRootPath}/$provider");
    }

    /**
     * Count route files the service provider will load for the module.
     *
     * Mirrors ModularizeServiceProvider::autoloadRoutes(), including its directory branch:
     * a *directory* named routes.php or Routes/web.php has every file inside it included,
     * so it counts as that many. Filesystem::exists() is true for directories too, hence
     * the isDirectory() check must come first.
     */
    protected function countRouteFiles(string $module): int
    {
        $count = 0;

        foreach (ModularizeServiceProvider::ROUTE_FILES as $routeFile) {
            $path = "{$this->moduleRootPath}/{$module}/{$routeFile}";

            if ($this->files->isDirectory(directory: $path)) {
                $count += count($this->files->allFiles(directory: $path));
            } elseif ($this->files->exists(path: $path)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count files matching a pattern anywhere beneath a module directory.
     */
    protected function countFiles(string $module, string $directory, string $pattern): int
    {
        $path = "{$this->moduleRootPath}/{$module}/{$directory}";

        if (! $this->files->isDirectory(directory: $path)) {
            return 0;
        }

        try {
            return iterator_count((new Finder)->in($path)->files()->name($pattern));
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Build the artifact rows shown for a module under `-v`.
     *
     * The service provider is always reported, because its absence changes whether the
     * module boots. Everything else is omitted when absent, so the output stays
     * proportional to what the module actually contains.
     *
     * @return array<string, string>
     */
    protected function getModuleDetails(string $module): array
    {
        $details = [
            'Service Provider' => $this->hasServiceProvider($module) ? '✓' : '✗',
        ];

        foreach (self::ARTIFACT_FILES as $label => $file) {
            if ($this->files->exists("{$this->moduleRootPath}/{$module}/{$file}")) {
                $details[$label] = '✓';
            }
        }

        if ($routes = $this->countRouteFiles($module)) {
            $details['Routes'] = (string) $routes;
        }

        foreach (self::ARTIFACT_DIRECTORIES as $label => [$directory, $pattern]) {
            if ($count = $this->countFiles($module, $directory, $pattern)) {
                $details[$label] = (string) $count;
            }
        }

        return $details;
    }

    /**
     * Display modules as a compact two-column table.
     *
     * Deliberate divergence from the package's twoColumnDetail house style: that component
     * pads every row to the terminal width, which for a plain name/path list produces a
     * screen of dot leaders. $this->table() appears nowhere else in src/ — intentional,
     * not an oversight.
     *
     * @param  array<int, array{name: string, path: string}>  $modules
     */
    protected function displayModulesTable(array $modules): void
    {
        $rows = [
            ['<fg=gray>Module</>', '<fg=gray>Path</>'],
            new TableSeparator,
        ];

        foreach ($modules as $module) {
            $rows[] = ['<fg=green>'.$module['name'].'</>', $module['path']];
        }

        $this->table([], $rows, $this->compactTableStyle());
    }

    /**
     * A table style with a single rule under the header and no outer border.
     *
     * Hand-built because no registered Symfony style produces this shape.
     * Table::renderRowSeparator() uses the OUTSIDE horizontal char for the top border, the
     * header rule AND the bottom border, and the INSIDE char only for TableSeparator rows —
     * so the header is passed as an ordinary row followed by a separator, and the outer
     * borders are collapsed to empty strings.
     *
     * TableStyle::setDisplayOutsideBorder(false) would be shorter but is Symfony >= 7.3;
     * CI tests laravel/framework 11.* with --prefer-lowest, which resolves symfony/console
     * 7.0 and would fatal on the undefined method.
     */
    protected function compactTableStyle(): TableStyle
    {
        return (new TableStyle)
            ->setHorizontalBorderChars('', '─')
            ->setVerticalBorderChars(' ')
            ->setCrossingChars(
                cross: ' ',
                topLeft: '',
                topMid: '',
                topRight: '',
                midRight: ' ',
                bottomRight: '',
                bottomMid: '',
                bottomLeft: '',
                midLeft: ' ',
            );
    }

    /**
     * Display each module with a breakdown of the artifacts it contains.
     */
    protected function displayModuleDetails(array $modules): void
    {
        $this->components->twoColumnDetail('<fg=gray>Module</>', '<fg=gray>Path</>');
        $this->newLine();

        foreach ($modules as $module) {
            $this->components->twoColumnDetail(
                '<fg=green>'.$module['name'].'</>',
                $module['path']
            );

            foreach ($this->getModuleDetails($module['name']) as $label => $value) {
                $this->components->twoColumnDetail(self::DETAIL_INDENT.$label, $value);
            }

            $this->newLine();
        }
    }
}
