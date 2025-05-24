<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeMiddlewareCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:middleware
                            {name : The name of the middleware}
                            {--module= : Name of module middleware should belong to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate middleware for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Middleware';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return true;
        }

        $type = '';
        $this->setStubFile("middleware.{$type}");
        $this->makeDirectory($path);

        $stub = $this->buildClass($name);

        $this->files->put($path, $stub);

        $this->logFileCreated($name);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Middleware';
    }
}
