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
                            {--markdown= : Create a new Markdown template for the mailable}
                            {--force : Create the class even if the component already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mail for a module';

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

        if (! $path = $this->getFilePath(name: $name, force: $this->option('force'))) {
            return true;
        }

        $type = '';

        $this->generateFile($path, $name, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Mail';
    }
}
