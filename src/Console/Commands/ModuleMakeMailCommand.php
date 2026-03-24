<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakeMailCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:mail
                            {name : The name of the mail}
                            {--module= : Name of module mail should belong to}
                            {--markdown= : Create a new Markdown template for the mailable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mail class for module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mail';

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

        $this->setStubFile('mail.');
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->logFileCreated($name);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Mail';
    }
}
