<?php

namespace Madeweb\ServiceLayer\Console\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service layer class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('model')) {
            $stub = '/stubs/service.model.stub';
        }else{
            $stub = '/stubs/service.plain.stub';
        }
        return __DIR__.$stub;
    }


    /**
     * @return string
     */
    protected function getBaseStub()
    {
        return __DIR__.'/stubs/service.base.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Services';
    }


    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        $this->buildBaseClass($path, $name);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type.' created successfully.');
    }

    /**
     * @param $path
     * @param $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildBaseClass($path, $name)
    {
        $serviceNamespace = $this->getNamespace($name);
        if (! class_exists($serviceNamespace.'\BaseService')) {
            $stub = $this->files->get($this->getBaseStub());
            $this->replaceNamespace($stub, $name);
            $serviceNamespace = Str::replaceFirst($this->rootNamespace(), '', $serviceNamespace);
            $this->files->put($this->laravel['path'].'/'.str_replace('\\', '/', $serviceNamespace).'/BaseService.php', $stub);
        }
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base service import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $serviceNamespace = $this->getNamespace($name);
        $replace = [];
        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }
        $replace["use {$serviceNamespace}\Service;\n"] = '';
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function buildBaseReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('base'));
        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource service for the given model.']
        ];
    }
}
