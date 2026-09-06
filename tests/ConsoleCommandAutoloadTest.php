<?php

namespace NorbyBaru\Modularize\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class ConsoleCommandAutoloadTest extends TestCase
{
    protected Filesystem $files;

    protected string $moduleName = 'Blog';

    /** @var (callable(string): void)|null */
    private $modulesAutoloader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->cleanUp();
        $this->registerModulesAutoloader();
    }

    protected function tearDown(): void
    {
        if ($this->modulesAutoloader) {
            spl_autoload_unregister($this->modulesAutoloader);
            $this->modulesAutoloader = null;
        }

        $this->cleanUp();

        parent::tearDown();
    }

    public function test_it_should_register_console_command_from_module()
    {
        $this->writeConsoleCommand(file: 'SendEmails.php', class: 'SendEmails', signature: 'blog:send-emails');

        $this->bootModules();

        $this->assertArrayHasKey('blog:send-emails', Artisan::all());
    }

    public function test_it_should_register_console_command_in_nested_directory()
    {
        $this->writeConsoleCommand(file: 'Reports/GenerateReport.php', class: 'GenerateReport', signature: 'blog:generate-report');

        $this->bootModules();

        $this->assertArrayHasKey('blog:generate-report', Artisan::all());
    }

    public function test_it_should_not_register_abstract_console_command()
    {
        $this->writeConsoleFile('AbstractReport.php', <<<'PHP'
        <?php

        namespace Modules\Blog\Console;

        use Illuminate\Console\Command;

        abstract class AbstractReport extends Command
        {
            protected $signature = 'blog:abstract-report';

            protected $description = 'Fixture abstract command';
        }

        PHP);

        $this->bootModules();

        $this->assertArrayNotHasKey('blog:abstract-report', Artisan::all());
    }

    public function test_it_should_ignore_classes_that_are_not_console_commands()
    {
        $this->writeConsoleCommand(file: 'PruneLogs.php', class: 'PruneLogs', signature: 'blog:prune-logs');
        $this->writeConsoleFile('LogFormatter.php', <<<'PHP'
        <?php

        namespace Modules\Blog\Console;

        class LogFormatter
        {
            public function format(string $line): string
            {
                return trim($line);
            }
        }

        PHP);

        $this->bootModules();

        $this->assertArrayHasKey('blog:prune-logs', Artisan::all());
    }

    public function test_it_should_boot_when_console_directory_contains_non_php_files()
    {
        $this->writeConsoleCommand(file: 'ArchivePosts.php', class: 'ArchivePosts', signature: 'blog:archive-posts');
        $this->writeConsoleFile('README.md', "# Console commands\n");
        $this->writeConsoleFile('LICENSE', "MIT\n");

        $this->bootModules();

        $this->assertArrayHasKey('blog:archive-posts', Artisan::all());
    }

    /**
     * Boot a fresh application so the service provider discovers the files just written.
     */
    private function bootModules(): void
    {
        $this->refreshApplication();
    }

    /**
     * Mirrors the `"Modules\\": "modules/"` PSR-4 entry the README asks applications to add.
     */
    private function registerModulesAutoloader(): void
    {
        $root = base_path(config('modularize.root_path'));

        $this->modulesAutoloader = function (string $class) use ($root): void {
            if (! str_starts_with($class, 'Modules\\')) {
                return;
            }

            $file = $root.'/'.str_replace('\\', '/', substr($class, strlen('Modules\\'))).'.php';

            if (is_file($file)) {
                require_once $file;
            }
        };

        spl_autoload_register($this->modulesAutoloader);
    }

    private function writeConsoleCommand(string $file, string $class, string $signature): void
    {
        $namespace = "Modules\\{$this->moduleName}\\Console";

        if (str_contains($file, '/')) {
            $namespace .= '\\'.str_replace('/', '\\', dirname($file));
        }

        $this->writeConsoleFile($file, <<<PHP
        <?php

        namespace {$namespace};

        use Illuminate\Console\Command;

        class {$class} extends Command
        {
            protected \$signature = '{$signature}';

            protected \$description = 'Fixture command';

            public function handle(): int
            {
                return self::SUCCESS;
            }
        }

        PHP);
    }

    private function writeConsoleFile(string $file, string $contents): void
    {
        $path = $this->getModulePath($this->moduleName)."/Console/{$file}";

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $contents);
    }

    private function cleanUp(): void
    {
        $this->files->deleteDirectory(base_path(config('modularize.root_path')));
    }
}
