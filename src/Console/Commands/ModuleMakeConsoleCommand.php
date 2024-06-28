<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeConsoleCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:console
                        {name : The name of the command}
                        {--module= : Name of module controller should belong to}
                        {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate console command for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Console';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $this->generateFile($path, $name);

    }

    protected function logFileCreated(string $path, ?string $type = null)
    {
        parent::logFileCreated($path, 'Console command');
    }

    protected function getFolderPath(): string
    {
        return 'Console';
    }
}
