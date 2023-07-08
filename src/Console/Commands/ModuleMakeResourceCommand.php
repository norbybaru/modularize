<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeResourceCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:resource 
                            {name : The name of the resource}
                            {--module= : Name of module migration should belong to}
                            {--collection : Create a resource collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate resource for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass('Modules\\'. $module .'\\' . $folder . '\\'. $filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);
            return;
        }

        $type = "";
        if ($this->option('collection')) {
            $type = "collection.";
        }

        $this->setStubFile("resource.{$type}");
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);   
    }

    protected function getFolderPath(): string
    {
        return 'Resources';
    }
}