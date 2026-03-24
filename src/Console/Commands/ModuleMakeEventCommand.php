<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeEventCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:event
                            {name : The name of the event}
                            {--module= : Name of module event should belong to}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate event for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Event';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $this->generateFile($path, $name);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Events';
    }
}
