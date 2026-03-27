<?php

namespace NorbyBaru\Modularize\Console\Commands;

class ModuleMakeSeederCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:seeder
                {name : The name of the seeder}
                {--module= : Name of module seeder should belong to}
                {--model= : The name of the model for the seeder}
                {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeder for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

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

            $this->setStubFile("seeder.{$type}");
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
        return 'Database\\Seeders';
    }
}
