<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakePolicyCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:policy
                            {name : The name of the policy}
                            {--module= : Name of module policy should belong to}
                            {--model= : The model that the policy applies to}
                            {--guard= : The guard that the policy relies on}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate policy for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Policy';

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

        if ($model = $this->option('model')) {
            $type = 'model.';
            $model = $this->qualifyClass($module.'\\'.'Models'.'\\'.$model);
        }

        if ($model) {
            $this->setStubFile("policy.{$type}");
            $this->makeDirectory($path);

            $stub = $this->buildClass($name);
            $this->files->put($path, $this->buildModel($stub, $model));
            $this->logFileCreated($name);

            return null;
        }

        $this->generateFile($path, $name, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Policies';
    }
}
