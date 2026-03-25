<?php

namespace NorbyBaru\Modularize\Console\Commands;

class ModuleMakeFactoryCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:factory
                {name : The name of the factory}
                {--module= : Name of module policy should belong to}
                {--model= : The name of the model for the factory}
                {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate factory for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = $this->getNameInput();
        $folder = $this->getFolderPath();

        $filename = $this->appendSuffix(filename: $filename);

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $type = '';

        if ($model = $this->option('model')) {
            $model = $this->qualifyClass($module.'\\'.'Models'.'\\'.$model);
        }

        if ($model) {
            $this->setStubFile("factory.{$type}");
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
        return 'Database\\Factories';
    }
}
