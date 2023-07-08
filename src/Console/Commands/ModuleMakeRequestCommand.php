<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeRequestCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:request 
                            {name : The name of the request}
                            {--module= : Name of module migration should belong to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate form request for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    public function handle()
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass('Modules\\'.$module.'\\'.$folder.'\\'.$filename);

        if ($this->files->exists($path = $this->getPath($name))) {
            $this->logFileExist($name);

            return;
        }

        $this->setStubFile('request.');
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);
    }

    protected function getFolderPath(): string
    {
        return 'Requests';
    }
}
