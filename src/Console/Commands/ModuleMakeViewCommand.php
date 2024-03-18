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
                            {--pest : Generate an accompanying Pest test for the View}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate view resource for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'view';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = $this->getNameInput();
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name, 'blade.php'))) {
            $this->logFileExist($name);

            return true;
        }

        $type = '';

        $this->setStubFile("view.{$type}");
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);

        $folder = 'Tests/Feature';
        if ($this->option('pest')) {
            $this->call(
                ModuleMakeTestCommand::class,
                [
                    'name' => ($filename),
                    '--module' => $module,
                    '--pest' => true,
                    '--view' => true,
                ]
            );
            $type = 'pest.';
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
            $type = 'test.';
        }

        return true;
    }

    protected function getFolderPath(): string
    {
        return 'Views';
    }
}
