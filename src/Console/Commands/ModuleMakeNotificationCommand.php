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
                            {--guard= : The guard that the policy relies on}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notification for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Notification';

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
        return 'Notifications';
    }
}
