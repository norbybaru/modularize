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
                {--model= : The name of the model for the seeder}';

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

        if (! $path = $this->getFilePath(name: $name)) {
            return true;
        }

        $type = '';
        $model = null;

        if ($modelOption = $this->option('model')) {
            $model = $this->qualifyClass($module.'\\'.'Models'.'\\'.$modelOption);
        }

        $this->generateFileWithModel($path, $name, $model, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Database\\Seeders';
    }
}
