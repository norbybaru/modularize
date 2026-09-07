<?php

namespace NorbyBaru\Modularize\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use NorbyBaru\Modularize\Tests\MakeCommandTestCase;

class ModuleListCommandTest extends MakeCommandTestCase
{
    protected function teardown(): void
    {
        $this->resetShellVerbosity();

        parent::tearDown();
    }

    public function test_it_fails_when_modules_directory_does_not_exist()
    {
        $this->cleanUp();

        $this->artisan('module:list')
            ->expectsOutputToContain('Modules directory does not exist')
            ->assertFailed();
    }

    public function test_it_shows_info_message_when_no_modules_found()
    {
        $this->files->ensureDirectoryExists($this->getModulePath(''));

        $this->artisan('module:list')
            ->expectsOutputToContain('No modules found.')
            ->assertSuccessful();
    }

    public function test_it_lists_a_single_module()
    {
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->artisan('module:list')
            ->expectsOutputToContain('modules/'.$this->moduleName)
            ->expectsOutputToContain('Total modules: 1')
            ->assertSuccessful();
    }

    public function test_it_lists_multiple_modules()
    {
        foreach (['Blog', 'Shop', 'Forum'] as $module) {
            $this->files->ensureDirectoryExists($this->getModulePath($module));
        }

        $this->artisan('module:list')
            ->expectsOutputToContain('modules/Shop')
            ->expectsOutputToContain('Total modules: 3')
            ->assertSuccessful();
    }

    public function test_it_shows_only_name_and_path_by_default()
    {
        $this->writeArtifact('Jobs/SendDigest.php');

        $this->artisan('module:list')
            ->expectsOutputToContain('modules/'.$this->moduleName)
            ->doesntExpectOutputToContain('Service Provider')
            ->doesntExpectOutputToContain('Jobs')
            ->assertSuccessful();
    }

    public function test_it_shows_artifact_detail_when_verbose()
    {
        $this->writeArtifact('Jobs/SendDigest.php');

        $output = $this->listOutput(verbose: true);

        $this->assertDetail($output, 'Jobs', '1');
    }

    public function test_it_always_reports_service_provider_even_when_absent()
    {
        $this->files->ensureDirectoryExists($this->getModulePath($this->moduleName));

        $this->assertDetail($this->listOutput(verbose: true), 'Service Provider', '✗');
    }

    public function test_it_detects_service_provider()
    {
        $this->writeArtifact("Providers/{$this->moduleName}ServiceProvider.php");

        $this->assertDetail($this->listOutput(verbose: true), 'Service Provider', '✓');
    }

    public function test_it_omits_artifact_types_the_module_does_not_have()
    {
        $this->writeArtifact('Jobs/SendDigest.php');

        $output = $this->listOutput(verbose: true);

        $this->assertStringContainsString('Jobs', $output);
        $this->assertStringNotContainsString('Notifications', $output);
        $this->assertStringNotContainsString('Migrations', $output);
        $this->assertStringNotContainsString('Views', $output);
    }

    public function test_it_counts_route_files()
    {
        $this->writeArtifact('routes.php');
        $this->writeArtifact('Routes/web.php');
        $this->writeArtifact('Routes/api.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Routes', '3');
    }

    public function test_it_does_not_count_route_files_the_provider_never_loads()
    {
        $this->writeArtifact('Routes/web.php');
        $this->writeArtifact('Routes/admin.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Routes', '1');
    }

    public function test_it_counts_every_file_when_a_route_path_is_a_directory()
    {
        // The provider includes every file inside a *directory* named Routes/web.php, so the
        // count must match. Filesystem::exists() is also true for directories, which would
        // otherwise report 1 here.
        $this->writeArtifact('Routes/web.php/posts.php');
        $this->writeArtifact('Routes/web.php/tags.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Routes', '2');
    }

    public function test_it_counts_components()
    {
        $this->writeArtifact('Components/Alert.php');
        $this->writeArtifact('Components/Button.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Components', '2');
    }

    public function test_it_counts_migrations_in_the_lowercase_directory()
    {
        $this->writeArtifact('Database/migrations/2026_01_01_000000_create_posts_table.php');
        $this->writeArtifact('Database/migrations/2026_01_02_000000_create_tags_table.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Migrations', '2');
    }

    public function test_it_counts_artifacts_in_nested_directories()
    {
        $this->writeArtifact('Controllers/PostController.php');
        $this->writeArtifact('Controllers/Api/PostController.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Controllers', '2');
    }

    public function test_it_counts_component_views_under_views()
    {
        $this->writeArtifact('Views/index.blade.php');
        $this->writeArtifact('Views/Components/alert.blade.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Views', '2');
    }

    public function test_it_reports_config_and_helper_by_presence()
    {
        $this->writeArtifact('config.php');
        $this->writeArtifact('helper.php');

        $output = $this->listOutput(verbose: true);

        $this->assertDetail($output, 'Config', '✓');
        $this->assertDetail($output, 'Helper', '✓');
    }

    public function test_it_lists_module_with_all_features()
    {
        $this->writeArtifact("Providers/{$this->moduleName}ServiceProvider.php");
        $this->writeArtifact('routes.php');
        $this->writeArtifact('Components/Card.php');
        $this->writeArtifact('Models/Post.php');

        $output = $this->listOutput(verbose: true);

        $this->assertDetail($output, 'Service Provider', '✓');
        $this->assertDetail($output, 'Routes', '1');
        $this->assertDetail($output, 'Components', '1');
        $this->assertDetail($output, 'Models', '1');
    }

    /**
     * A plain run must be lean even in a process that already ran with `-v`.
     *
     * This is not hypothetical: on symfony/console < 8 the verbosity set by one
     * Artisan::call() persists into the next call in the same process, so without the
     * SHELL_VERBOSITY reset in listOutput() the second run here returns detail. Caught by
     * CI on Laravel 11/12 --prefer-lowest; not reproducible on symfony/console 8.x, which
     * restores the previous value in Application::run()'s finally block.
     */
    public function test_verbosity_does_not_persist_between_runs()
    {
        $this->writeArtifact('Jobs/SendDigest.php');

        $this->assertDetail($this->listOutput(verbose: true), 'Jobs', '1');

        $this->assertStringNotContainsString('Service Provider', $this->listOutput());
    }

    /**
     * Run the command and return its raw output.
     *
     * Detail rows are asserted against raw output rather than expectsOutputToContain for two
     * reasons: twoColumnDetail pads between label and value with dots, so the pair is never a
     * contiguous substring; and PendingCommand registers one Mockery expectation per expected
     * substring, so an earlier substring swallows every write that also matches a later one.
     */
    private function listOutput(bool $verbose = false): string
    {
        $this->resetShellVerbosity();
        $this->withoutMockingConsoleOutput();

        $this->artisan('module:list', $verbose ? ['-v' => true] : []);

        return Artisan::output();
    }

    /**
     * Clear the verbosity Symfony stashes in the environment.
     *
     * Application::configureIO() writes SHELL_VERBOSITY to putenv/$_ENV/$_SERVER and its
     * default branch reads it back, so verbosity from one run bleeds into the next.
     * symfony/console 8.x restores the previous value itself, but 7.x does not — without
     * this, `-v` tests contaminate every later run in the same process on Laravel 11/12.
     */
    private function resetShellVerbosity(): void
    {
        putenv('SHELL_VERBOSITY');
        unset($_ENV['SHELL_VERBOSITY'], $_SERVER['SHELL_VERBOSITY']);
    }

    /**
     * Assert a `label ....... value` detail row was rendered.
     */
    private function assertDetail(string $output, string $label, string $value): void
    {
        $this->assertMatchesRegularExpression(
            '/'.preg_quote($label, '/').'[\s.]*'.preg_quote($value, '/').'/u',
            $output,
            "Expected detail row [{$label} => {$value}] in output:\n{$output}"
        );
    }

    /**
     * Write a file at a module-relative path, creating parent directories as needed.
     */
    private function writeArtifact(string $relativePath, string $contents = "<?php\n"): void
    {
        $path = $this->getModulePath($this->moduleName).'/'.$relativePath;

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $contents);
    }
}
