<?php

namespace NorbyBaru\Modularize\Console\Commands;

class ModuleMakeViewCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:view
                            {name : The name of the view}
                            {--module= : Name of module migration should belong to}
                            {--test : Generate an accompanying PHPUnit test for the View}
                            {--pest : Generate an accompanying Pest test for the View}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate view for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'view';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = $this->getNameInput();
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        // Custom extension handling for blade.php
        $path = $this->getPath($name, 'blade.php');
        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->logFileExist($path);

            return true;
        }

        $type = '';

        $this->generateFile($path, $name, $type);

        if ($this->option('pest')) {
            $this->call(
                ModuleMakeTestCommand::class,
                [
                    'name' => $filename,
                    '--module' => $module,
                    '--pest' => true,
                    '--view' => true,
                ]
            );
        }

        if ($this->option('test')) {
            $this->call(
                ModuleMakeTestCommand::class,
                [
                    'name' => $filename,
                    '--module' => $module,
                    '--view' => true,
                ]
            );
        }

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Views';
    }
}
