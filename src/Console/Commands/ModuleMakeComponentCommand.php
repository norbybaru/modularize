<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Str;

class ModuleMakeComponentCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:component
                        {name : The name of the component}
                        {--module= : Name of module controller should belong to}
                        {--inline : Create a component that renders an inline view}
                        {--view: Create an anonymous component with only a view}
                        {--test : Generate an accompanying PHPUnit test for the Component}
                        {--pest : Generate an accompanying Pest test for the Component}
                        {--force|f : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate view component for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Component';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);
        $path = $this->getPath($name);
        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->logFileExist($name);

            return true;
        }

        $type = '';

        if ($this->option('inline')) {
            $type = 'inline.';
        }

        $this->setStubFile("view-component.{$type}");
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        if (! $this->option('inline')) {
            $this->call(
                ModuleMakeViewCommand::class,
                [
                    'name' => 'Components/'.Str::snake($filename, '-'),
                    '--module' => $module,
                    '--quiet' => true,
                ]
            );
        }

        $this->logFileCreated($name);

        return true;
    }

    protected function buildClass($name): string
    {
        if ($this->option('inline')) {
            return str_replace(
                ['{{view}}', '{{ view }}'],
                "<<<'blade'\n<div>\n    <!-- ".Inspiring::quotes()->random()." -->\n</div>\nblade",
                parent::buildClass($name)
            );
        }

        return parent::buildClass($name);
    }

    protected function getFolderPath(): string
    {
        return 'Components';
    }
}
