<?php

namespace Acioina\UserManagement\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ExcelClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user-management:clearExcelFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Excel files';

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
        $path = $GLOBALS['CIOINA_Config']->get('MoxieManagerBaseDir') . DIRECTORY_SEPARATOR . config('expendable.manager_root_dir');

        if (! $path) {
            throw new RuntimeException('MoxieManagerBaseDir and expendable.manager_root_dir not found.');
        }
        foreach ($this->files->glob("{$path}/*") as $excel) {
            $this->files->delete($excel);
        }

        $this->info('Excel files cleared!');
    }
}
