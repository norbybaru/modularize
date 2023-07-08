<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeModelCommand extends ModuleMakerCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:model 
                        {name : The name of the model}
                        {--module= : Name of module controller should belong to}
                        {--a|all : Generate a migration, seeder, factory, policy, resource controller, and form request classes for the model}
                        {--c|controller : Create a new controller for the model}
                        {--f|factory : Create a new factory for the model}
                        {--m|migration : Create a new migration file for the model}
                        {--p|pivot : Indicates if the generated model should be a custom intermediate table model}
                        {--policy :  Create a new policy for the model}
                        {--s|seed : Create a new seeder for the model}
                        {--api : Exclude the create and edit methods from the controller}
                        {--i|invokable : Generate a single method, invokable controller class}
                        {--r|resource : Generate a resource controller class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate model for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $type = "";

        if ($this->option('pivot')) {
            $type = 'pivot.';
        }

        $name = $this->qualifyClass('Modules\\'. $module .'\\' . $folder . '\\'. $filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);
            return;
        }

        $this->setStubFile("model.{$type}");
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        if ($this->option('migration')) {
           $this->makeMigration(name: $filename, module: $module);
        }

        if ($this->option('controller')) {
            $this->makeController(name: $filename, module: $module);
        }

        if ($this->option('policy')) {
            $this->makePolicy(name: $filename, module: $module);
        }

        $this->logFileCreated($name);
    }

    private function makeAll()
    {
        //TODO: makeAll() implementation
    }

    private function makeController(string $name, string $module): void
    {
        $args = [
            'name' => "{$name}Controller",
            '--module' => $module,
        ];

        if ($this->option('api')) {
            $args['--api'] = true;
        }

        if ($this->option('invokable')) {
            $args['--invokable'] = true;
        }

        if ($this->option('resource')) {
            $args['--resource'] = true;
        }

        $this->call(
            command: 'module:make:controller',
            arguments: $args
        );
    }

    private function makeMigration(string $name, string $module): void
    {
        $this->call(
            command: 'module:make:migration',
            arguments: [
                'name' => "create_{$this->getPluralName($name)}_table",
                '--create' => $name,
                '--module' => $module,
            ]
        );
    }

    private function makePolicy(string $name, string $module)
    {
        $this->call(
            command: 'module:make:policy',
            arguments: [
                'name' => $name,
                '--module' => $module,
                '--model' => $name,
            ]
        );
    }

    private function makeFactory()
    {
        //TODO: makeFactory() implementation
    }

    private function makeSeed()
    {
        //TODO: makeSeed() implementation
    }

    protected function getFolderPath(): string
    {
        return 'Models';
    }

    protected function setStubFile(string $file): void
    {
        $this->currentStub = $this->currentStub . $file . "sample";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            ['name', InputArgument::REQUIRED, 'The name of the model'],
        );
    }
}