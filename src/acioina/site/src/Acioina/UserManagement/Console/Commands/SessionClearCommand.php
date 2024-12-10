<?php

namespace Acioina\UserManagement\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SessionClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user-management:clearLaravelSession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Laravel session and view files and PHP session files';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $path = $this->laravel['config']['view.compiled'];
        if (! $path) {
            throw new RuntimeException('View path not found.');
        }
        foreach ($this->files->glob("{$path}/*") as $view) {
            $this->files->delete($view);
        }

        $path = $this->laravel['config']['session.files'];
        if (! $path) {
            throw new RuntimeException('Session path not found.');
        }
        foreach ($this->files->glob("{$path}/*") as $session) {
            $this->files->delete($session);
        }

        $path = $GLOBALS['CIOINA_Config']->get('PhpSessionsTemp');
        foreach ($this->files->glob("{$path}/sess_*") as $session) {
            $this->files->delete($session);
        }

        $path = $GLOBALS['CIOINA_Config']->get('TwigTempDir');
        foreach ($this->files->glob("{$path}/*") as $session) {
            if($this->files->isDirectory($session))
            {
                $this->files->deleteDirectory($session);
            }else{
                $this->files->delete($session);
            }
        }

        $this->info('Twig, Laravel, and PHP session files cleared!');
    }
}
