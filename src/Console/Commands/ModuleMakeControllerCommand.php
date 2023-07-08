<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

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
                            {--m|model= : Generate a resource controller for the given model}';

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

    public function handle()
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

        $name = $this->qualifyClass('Modules\\'.$module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return;
        }

        $this->setStubFile("controller.{$type}");
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);
    }

    protected function getFolderPath(): string
    {
        return 'Controllers';
    }

    protected function setStubFile(string $file): void
    {
        $this->currentStub = $this->currentStub.$file.'sample';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller'],
        ];
    }
}
