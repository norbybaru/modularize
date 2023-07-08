<?php

namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

abstract class ModuleMakerCommand extends GeneratorCommand
{
    /**
     * The current stub.
     *
     * @var string
     */
    protected $currentStub = __DIR__ . '/templates/';

     /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->currentStub;
    }

    public function getModuleInput(): string
    {
        if (!$module = $this->option('module')) {
            $module = $this->ask('What is the name of the module?');
        }

        return $module;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        
        return $this->replaceName($stub, $this->getNameInput())
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
                '{{namespacedUserModel}}'
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

        $stub = str_replace(search: 'SampleTitle', replace: strtolower($name), subject: $stub);
        $stub = str_replace(search: 'SampleViewTitle', replace: strtolower(Str::snake($title, '-')), subject: $stub);
        $stub = str_replace(search: 'SampleUCtitle', replace: ucfirst(Str::studly($name)), subject: $stub);

        $stub = $this->removePrefixFromRoutes($stub);

        return $this;
    }

    /**
     * Remove prefix from routes when there its not a module group
     *
     * @param $stub
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
                'SampleClass'
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
        $user = class_basename( $this->userProviderModel());
        return str_replace(
            search: [
                '{{ model }}',
                '{{model}}',
                'model',
                '{{ user }}',
                '{{user}}'
            ],
            replace: [
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
                '{{namespacedModel}}'
            ],
            replace: $this->getNamespace($name),
            subject: $stub
        );

        return $this;
    }

    protected function replaceModelName(&$stub, $name): self
    {
        $stub = str_replace(
            search: [
                '{{ modelVariable }}',
                '{{modelVariable}}'
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

    protected function getPluralName(string $name): string
    {
        return Str::of($name)
            ->plural()
            ->snake();
    }

    protected function setStubFile(string $file): void
    {
        $this->currentStub = $this->currentStub . $file . "sample";
    }

    protected function logFileCreated(string $path)
    {
        $this->components->info(sprintf('%s [%s] created successfully.', $this->type, $path));
    }

    protected function logFileExist(string $path)
    {
        $this->components->error(sprintf('%s [%s] already exist.', $this->type, $path));
    }

    abstract protected function getFolderPath(): string;
}