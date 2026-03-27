<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;

class ModuleMakePolicyCommand extends ModuleMakerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make:policy
                            {name : The name of the policy}
                            {--module= : Name of module policy should belong to}
                            {--model= : The model that the policy applies to}
                            {--guard= : The guard that the policy relies on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate policy for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Policy';

    public function handle(): ?bool
    {
        $module = $this->getModuleInput();
        $filename = Str::studly($this->getNameInput());
        $folder = $this->getFolderPath();

        $name = $this->qualifyClass($module.'\\'.$folder.'\\'.$filename);

        if (! $path = $this->getFilePath(name: $name)) {
            return true;
        }

        $type = '';
        $model = null;

        if ($modelOption = $this->option('model')) {
            $type = 'model.';
            $model = $this->qualifyClass($module.'\\'.'Models'.'\\'.$modelOption);
        }

        $this->generateFileWithModel($path, $name, $model, $type);

        return null;
    }

    protected function getFolderPath(): string
    {
        return 'Policies';
    }
}