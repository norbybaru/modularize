<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

abstract class ModuleMakerCommand extends GeneratorCommand
{
    /**
     * The current stub.
     *
     * @var string
     */
    protected $currentStub = __DIR__.'/templates/';

    protected ?string $module = null;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->currentStub;
    }

    private function getTemplatePath(string $file): string
    {
        return __DIR__."/templates/{$file}sample";
    }

    public function getModuleInput(): string
    {
        if (! $this->module = $this->option('module')) {
            $this->module = $this->ask('What is the name of the module?');
        }

        return $this->module;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceName($stub, $this->getNameInput())
            ->replaceModuleName($stub, $this->module)
            ->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);
    }

    protected function buildModel(string $stub, string $model): string
    {
        return $this->replaceModelName($stub, class_basename($model))
            ->replaceModelNamespace($stub, $model)
            ->replaceModelClass($stub, $model);
    }

    protected function buildClassWithModel(string $name, string $model): string
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceModelName($stub, $this->getNameInput())
            ->replaceModelNamespace($stub, $model)
            ->replaceModelClass($stub, $model);

        return $this->replaceName($stub, $this->getNameInput())
            ->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name): self
    {
        $stub = str_replace(
            search: [
                '{{ namespace }}',
                '{{namespace}}',
                'SampleNamespace',
                '{{ rootNamespace }}',
                '{{rootNamespace}}',
                'SampleRootNamespace',
                'NamespacedDummyUserModel',
                '{{ namespacedUserModel }}',
                '{{namespacedUserModel}}',
            ],
            replace: [
                $this->getNamespace($name),
                $this->getNamespace($name),
                $this->getNamespace($name),
                $this->rootNamespace(),
                $this->rootNamespace(),
                $this->rootNamespace(),
                $this->userProviderModel(),
                $this->userProviderModel(),
                $this->userProviderModel(),
            ],
            subject: $stub
        );

        return $this;
    }

    /**
     * Replace the name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceName(&$stub, $name)
    {
        $title = $name;

        $stub = str_replace(
            search: [
                'SampleTitle',
                'SampleViewTitle',
                'SampleUCtitle',
                '{{viewFile}}',
                '{{command}}',
            ],
            replace: [
                strtolower($name),
                strtolower(Str::snake($title, '-')),
                ucfirst(Str::studly($name)),
                strtolower(Str::snake(str_replace(search: '/', replace: '.', subject: $title), '-')),
                strtolower(str_replace(search: '/-', replace: ':', subject: Str::snake($title, '-'))),
            ],
            subject: $stub
        );

        $stub = $this->removePrefixFromRoutes($stub);

        return $this;
    }

    protected function replaceModuleName(&$stub, $name)
    {
        $stub = str_replace(
            search: [
                '{{ moduleName }}',
                '{{moduleName}}',
                'moduleName',
            ],
            replace: strtolower($name),
            subject: $stub
        );

        return $this;
    }

    /**
     * Remove prefix from routes when there its not a module group
     *
     * @return mixed
     */
    private function removePrefixFromRoutes(&$stub)
    {
        return str_replace(
            search: "'prefix' => 'SampleModuleGroup', ",
            replace: '',
            subject: $stub
        );
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = class_basename($name);

        return str_replace(
            search: [
                '{{ class }}',
                '{{class}}',
                'SampleClass',
            ],
            replace: [
                $class,
                $class,
                $class,
            ],
            subject: $stub
        );
    }

    protected function replaceModelClass($stub, $name): string
    {
        $class = class_basename($name);
        $user = class_basename($this->userProviderModel());

        return str_replace(
            search: [
                '{{ model }}',
                '{{model}}',
                'model',
                '{{ event }}',
                '{{event}}',
                '{{ user }}',
                '{{user}}',
            ],
            replace: [
                $class,
                $class,
                $class,
                $class,
                $class,
                $user,
                $user,
            ],
            subject: $stub
        );
    }

    protected function replaceModelNamespace(&$stub, $name)
    {
        $stub = str_replace(
            search: [
                '{{ namespacedModel }}',
                '{{namespacedModel}}',
                '{{ eventNamespace }}',
                '{{eventNamespace}}',
            ],
            replace: $this->getModelNamespace($name),
            subject: $stub
        );

        return $this;
    }

    protected function replaceModelName(&$stub, $name): self
    {
        $stub = str_replace(
            search: [
                '{{ modelVariable }}',
                '{{modelVariable}}',
            ],
            replace: [
                Str::of($name)->lower(),
                Str::of($name)->lower(),
            ],
            subject: $stub
        );

        return $this;
    }

    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        $name = str_replace(search: '\\routes\\', replace: '\\', subject: $name);

        return trim(
            implode(
                '\\',
                array_map(
                    'ucfirst',
                    array_slice(explode('\\', Str::studly($name)), 0, -1)
                )
            ),
            '\\'
        );
    }

    protected function getModelNamespace($name)
    {
        return trim(
            implode(
                '\\',
                array_map(
                    'ucfirst',
                    array_slice(explode('\\', Str::studly($name)), 0)
                )
            ),
            '\\'
        );
    }

    protected function getPluralName(string $name): string
    {
        return Str::of($name)
            ->plural()
            ->snake();
    }

    protected function getFilePath(string $name, bool $force = false): ?string
    {
        if ($this->files->exists($path = $this->getPath($name)) && ! $force) {
            $this->logFileExist($name);

            return null;
        }

        return $path;
    }

    protected function generateFile(string $path, string $filename, string $stubType = ''): void
    {
        $stubPrefix = strtolower($this->type);
        $this->setStubFile("{$stubPrefix}.{$stubType}");
        $this->makeDirectory($path);

        $stub = $this->buildClass($filename);

        $this->files->put($path, $stub);

        $this->logFileCreated($filename);
    }

    protected function setStubFile(string $file): void
    {
        $this->currentStub = $this->getTemplatePath($file);
    }

    protected function logFileCreated(string $path, ?string $type = null)
    {
        if (! $this->hasOption('quiet') || ! $this->option('quiet')) {
            $this->components->info(sprintf('%s [%s] created successfully.', $type ?? $this->type, $path));
        }
    }

    protected function logFileExist(string $path)
    {
        if (! $this->hasOption('quiet') || ! $this->option('quiet')) {
            $this->components->error(sprintf('%s [%s] already exist.', $this->type, $path));
        }
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name, string $fileExtension = 'php')
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->getModuleRootPath().'/'.str_replace('\\', '/', $name).".{$fileExtension}";
    }

    protected function getModuleRootPath(): string
    {
        return base_path(config('modularize.root_path'));
    }

    protected function getModuleRootDirectory(): string
    {
        return config('modularize.root_path');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config('modularize.root_path').'\\';
    }

    abstract protected function getFolderPath(): string;
}
