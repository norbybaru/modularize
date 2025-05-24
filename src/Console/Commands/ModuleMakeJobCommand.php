<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeJobCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:job
                            {name : The name of the job}
                            {--module= : Name of module job should belong to}
                            {--sync : Indicates that job should be synchronous}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate job for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath($name)) {
            return true;
        }

        $type = '';

        if ($this->option('sync')) {
            $type = 'sync.';
        }

        $this->generateFile($path, $name, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Jobs';
    }
}
