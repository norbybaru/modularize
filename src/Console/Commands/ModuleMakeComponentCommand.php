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
                        {--view : Create an anonymous component with only a view}
                        {--test : Generate an accompanying PHPUnit test for the Component}
                        {--pest : Generate an accompanying Pest test for the Component}
                        {--force : Create the class even if the component already exists}';

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

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $type = '';

        if ($this->option('inline')) {
            $type = 'inline.';
        }

        $this->generateFileWithCustomStub($path, $name, "view-component.{$type}");

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

        return null;
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
