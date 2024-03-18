<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeNotificationCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:notification 
                            {name : The name of the notification}
                            {--module= : Name of module migration should belong to}
                            {--model= : The model that the policy applies to}
                            {--guard= : The guard that the policy relies on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notification for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Notification';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return true;
        }

        $this->setStubFile('notification.');
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);

        return true;
    }

    protected function getFolderPath(): string
    {
        return 'Notifications';
    }
}
