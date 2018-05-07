<?php namespace NorbyBaru\Modularize\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:generate {name} {--no-migration : Do not create new migration files} {--no-translation : Do not create module translation filesystem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new module';

    /**
     * The current stub.
     *
     * @var string
     */
    protected $currentStub;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Module';

    /**
     * Laravel version
     *
     * @var string
     */
    protected $version;

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->version = (int) str_replace('.', '', app()->version());

        // check if module exists
        if ($this->files->exists(app_path().'/Modules/'.studly_case($this->getNameInput()))) {
            $this->error($this->type.' already exists!');
            return;
        }

        // Create Controller
        $this->generate('controller');

        // Create Model
        $this->generate('model');

        // Create Views folder
        $this->generate('view');

        // Create Helper file
        $this->generate('helper');

        if ($this->version < 530) {
            // Create Routes file
            $this->generate('routes');
        } else {
            // Create WEB Routes file
            $this->generate('web');

            // Create API Routes file
            $this->generate('api');
        }

        //Flag for no translation
        if (!$this->option('no-translation')) {
            $this->generate('translation');
        }

        //Flag for no migrations
        if (!$this->option('no-migration')) {
            // without hacky studly_case function
            // foo-bar results in foo-bar and not in foo_bar
            $table = str_plural(snake_case(studly_case($this->getNameInput())));
            $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
        }

        $this->info($this->type.' created successfully.');
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function fire()
    {
        return $this->handle();
    }

    /**
     * @param $type
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     */
    protected function generate($type)
    {
        switch ($type) {
            case 'controller':
                $filename = studly_case($this->getNameInput()).ucfirst($type);
                break;
            case 'model':
                $filename = studly_case($this->getNameInput());
                break;
            case 'view':
                $filename = 'index.blade';
                break;

            case 'translation':
                $filename = 'example';
                break;

            case 'routes':
                $filename = 'routes';
                break;
            case 'web':
                $filename = 'web';
                $folder = 'routes\\';
                break;
            case 'api':
                $filename = 'api';
                $folder = 'routes\\';
                break;

            case 'helper':
                $filename = 'Helper';
                break;
        }

        if (! isset($folder)) {
            $folder = ($type != 'routes' && $type != 'helper') ? ucfirst($type).'s\\'. ($type === 'translation' ? 'en\\':'') : '';
        }

        $qualifyClass = method_exists($this, 'qualifyClass') ? 'qualifyClass' : 'parseName';
        $name = $this->$qualifyClass('Modules\\'.studly_case(ucfirst($this->getNameInput())).'\\'.$folder.$filename);
        if ($this->files->exists($path = $this->getPath($name))) {
            $this->error($this->type.' already exists!');
            return;
        }
        $this->currentStub = __DIR__ . '/templates/' .$type.'.sample';
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
    }
    /**
     * Get the full namespace name for a given class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        $name = str_replace('\\routes\\', '\\', $name);
        return trim(implode('\\', array_map('ucfirst', array_slice(explode('\\', studly_case($name)), 0, -1))), '\\');
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

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['SampleNamespace', 'SampleRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name), $this->rootNamespace(), config('auth.providers.users.model')],
            $stub
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
        $stub = str_replace('SampleTitle', $name, $stub);
        $stub = str_replace('SampleUCtitle', ucfirst(studly_case($name)), $stub);
        return $this;
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
        return str_replace('SampleClass', $class, $stub);
    }
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->currentStub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            ['name', InputArgument::REQUIRED, 'Module name.'],
        );
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--no-migration', null, InputOption::VALUE_NONE, 'Do not create new migration files.'],
            ['--no-translation', null, InputOption::VALUE_NONE, 'Do not create module translation filesystem.'],
        ];
    }
}