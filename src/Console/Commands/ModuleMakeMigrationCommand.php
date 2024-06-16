<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeMigrationCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:migration 
                            {name : The name of the migration}
                            {--module= : Name of module migration should belong to}
                            {--create= : Name of the table to be created}
                            {--table= : Name of the table to be updated}
                            {--no-translation : Do not create module translation filesystem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration for a module';

    /**
     * The current stub.
     *
     * @var string
     */
    protected $currentStub = __DIR__.'/templates/';

    protected string $folder = 'Database\\migrations';

    public function handle()
    {
        if (! $module = $this->option('module')) {
            $module = $this->ask('What is the name of the module?');
        }

        $module = Str::studly($module);

        $name = Str::studly($this->getNameInput());

        $create = $this->option('create');
        $update = $this->option('table');

        $path = $this->qualifyClass($module.'\\'.$this->folder);
        $path = $this->classPath($path);

        $arguments = [
            'name' => $name,
            '--path' => $path,
        ];

        if ($create) {
            $arguments['--create'] = $this->getPluralName($create);
        } else {
            $arguments['--table'] = $this->getPluralName($update);
        }

        $this->call(
            'make:migration',
            $arguments
        );

    }

    protected function getFolderPath(): string
    {
        return 'Migrations';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function classPath($name)
    {
        return str_replace('\\', '/', $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->currentStub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the migration'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--module', null, InputOption::VALUE_REQUIRED, 'Name of module migration should belong to.'],
        ];
    }
}
