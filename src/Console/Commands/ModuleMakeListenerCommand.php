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
                            {--queued : Indicates the event listener should be queued }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate listener for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass('Modules\\'.$module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath($name)) {
            return true;
        }

        $type = '';

        if ($event = $this->option('event')) {
            $type = 'event.';
            $event = $this->qualifyClass($module.'\\'.'Events'.'\\'.$event);
        }

        if ($this->option('queued')) {
            $type .= 'queued.';
        }

        if ($event) {
            $this->setStubFile(strtolower($this->type).".{$type}");
            $this->makeDirectory($path);

            $stub = $this->buildClass($name);
            $this->files->put($path, $this->buildModel($stub, $event));
            $this->logFileCreated($name);

            return true;
        }

        $this->generateFile($path, $name, $type);

        return true;
    }

    protected function getFolderPath(): string
    {
        return 'Listeners';
    }
}
