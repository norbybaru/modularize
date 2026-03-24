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
                        {--force : Create the class even if the component already exists}
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

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $type = '';

        if ($this->option('pivot')) {
            $type = 'pivot.';
        }

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $this->setStubFile("model.{$type}");
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);

        // Handle --all flag by setting all related options
        if ($this->option('all')) {
            $this->input->setOption('migration', true);
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('policy', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
        }

        if ($this->option('migration')) {
            $this->makeMigration(name: $filename, module: $module);
        }

        if ($this->option('controller')) {
            $this->makeController(name: $filename, module: $module);
        }

        if ($this->option('policy')) {
            $this->makePolicy(name: $filename, module: $module);
        }

        if ($this->option('factory')) {
            $this->makeFactory(name: $filename, module: $module);
        }

        if ($this->option('seed')) {
            $this->makeSeeder(name: $filename, module: $module);
        }

        return null;
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

    private function makePolicy(string $name, string $module): void
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

    private function makeFactory(string $name, string $module): void
    {
        $this->call(
            command: 'module:make:factory',
            arguments: [
                'name' => $name,
                '--module' => $module,
                '--model' => $name,
            ]
        );
    }

    private function makeSeeder(string $name, string $module): void
    {
        $this->call(
            command: 'module:make:seeder',
            arguments: [
                'name' => $name,
                '--module' => $module,
            ]
        );
    }

    protected function getFolderPath(): string
    {
        return 'Models';
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
            ['name', InputArgument::REQUIRED, 'The name of the model'],
        ];
    }
}
