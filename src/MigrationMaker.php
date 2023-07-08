<?php

namespace NorbyBaru\Modularize;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MigrationMaker
{
    protected string $stub;

    /**
     * Create a new migration creator instance.
     *
     * @return void
     */
    public function __construct(protected Filesystem $files, protected string $customStubPath)
    {
        $this->files = $files;
        $this->customStubPath = $customStubPath;
    }

    public static function make(Filesystem $files, string $customStubPath): self
    {
        return new self($files, $customStubPath);

    }

    public function create(string $table, string $destinationPath, bool $create = false)
    {
        $table = $this->getName($table);
        $stub = self::getStub($table, $create);
        $path = self::getPath($table, $this->classPath($destinationPath));

        $this->files->put(
            $path, $this->populateStub($stub, $table)
        );

        return $path;
    }

    /**
     * Get the migration stub file.
     *
     * @param  string|null  $table
     * @param  bool  $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        if (is_null($table)) {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.sample')
                            ? $customPath
                            : $this->stubPath().'/migration.stub';
        } elseif ($create) {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.create.sample')
                            ? $customPath
                            : $this->stubPath().'/migration.create.stub';
        } else {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.update.sample')
                            ? $customPath
                            : $this->stubPath().'/migration.update.sample';
        }

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($stub, $table)
    {
        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        return $stub;
    }

    private function getName(string $name): string
    {
        return Str::of($name)
            ->plural()
            ->snake();
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.$this->getDatePrefix().'_create_'.$name.'_table.php';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function classPath($name)
    {
        $name = Str::replaceFirst('App\\', '', $name);

        return app_path().'/'.str_replace('\\', '/', $name);
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
}
