<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeListenerCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:listener
                            {name : The name of the listener}
                            {--module= : Name of module event should belong to}
                            {--event= : The event class being listened for}
                            {--queued : Indicates the event listener should be queued }
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate listener for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

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
        $event = null;

        if ($event = $this->option('event')) {
            $type = 'event.';
            $event = $this->qualifyClass($module.'\\'.'Events'.'\\'.$event);
        }

        if ($this->option('queued')) {
            $type .= 'queued.';
        }

        $this->generateFileWithModel($path, $name, $event, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Listeners';
    }
}
