<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeControllerCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:controller
                            {name : The name of the controller}
                            {--module= : Name of module controller should belong to}
                            {--api : Exclude the create and edit methods from the controller}
                            {--i|invokable : Generate a single method, invokable controller class}
                            {--r|resource : Generate a resource controller class}
                            {--m|model= : Generate a resource controller for the given model}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate controller for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $type = 'plain.';

        if ($this->option('api')) {
            $type = 'api.';
        }

        if ($this->option('invokable')) {
            $type = 'invokable.';
        }

        if ($this->option('resource')) {
            $type = '';
        }

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $this->generateFile($path, $name, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Controllers';
    }
}
