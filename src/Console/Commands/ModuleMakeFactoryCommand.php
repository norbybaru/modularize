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
                {--model= : The name of the model for the factory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate model factory for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    public function handle(): bool|null
    {
        $module = $this->getModuleInput();
        $filename = $this->getNameInput();
        $folder = $this->getFolderPath();

        $type = '';
        if ($model = $this->option('model')) {
            $model = $this->qualifyClass($module.'\\'.'Models'.'\\'.$model);
        }

        $filename = $this->appendSuffix(filename: $filename);

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return true;
        }

        $this->setStubFile("factory.{$type}");
        $this->makeDirectory($path);

        $stub = $this->buildClass($name);

        if ($model) {
            $this->files->put($path, $this->buildModel($stub, $model));
            $this->logFileCreated($name);

            return null;
        }

        $this->files->put($path, $stub);

        $this->logFileCreated($name);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Database\\Factories';
    }
}
