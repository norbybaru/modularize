<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ModuleClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modularize:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the cached module discovery manifest';

    /** @var Filesystem */
    protected $files;

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
        $cachePath = $this->getCachedModulesPath();

        if (! $this->files->exists($cachePath)) {
            $this->components->info('Module cache does not exist.');

            return self::SUCCESS;
        }

        $this->files->delete($cachePath);

        $this->components->info('Module cache cleared successfully.');
        $this->components->twoColumnDetail('Removed file', $cachePath);

        return self::SUCCESS;
    }

    /**
     * Get the path to the cached modules file.
     */
    protected function getCachedModulesPath(): string
    {
        return base_path(config('modularize.cache_path', 'bootstrap/cache/modularize.php'));
    }
}
