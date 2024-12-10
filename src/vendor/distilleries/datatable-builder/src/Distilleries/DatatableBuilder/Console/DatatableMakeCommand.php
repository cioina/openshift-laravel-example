<?php namespace Distilleries\DatatableBuilder\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Distilleries\DatatableBuilder\Console\Lib\Generators\DatatableGenerator;

class DatatableMakeCommand extends \Illuminate\Console\GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatable:make';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a datable builder class.';

    /**
     * @var DatatableGenerator
     */
    protected $formGenerator;

    public function __construct(Filesystem $files, DatatableGenerator $formGenerator)
    {
        parent::__construct($files);
        $this->formGenerator = $formGenerator;
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Full path for datatable class.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('fields', null, InputOption::VALUE_OPTIONAL, 'Fields for the datatable'),
        );
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $formGenerator = $this->formGenerator;

        $stub = str_replace(
            '{{class}}',
            $formGenerator->getClassInfo($name)->className,
            $stub
        );

        return str_replace(
            '{{fields}}',
            $formGenerator->getFieldsVariable($this->option('fields')),
            $stub
        );
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {

        $stub = str_replace(
            '{{namespace}}',
            $this->formGenerator->getClassInfo($name)->namespace,
            $stub
        );

        return $this;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return str_replace('/', '\\', $this->argument('name'));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Lib/stubs/datatable-class-template.stub';
    }
}
