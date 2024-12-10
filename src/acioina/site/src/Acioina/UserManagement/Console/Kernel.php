<?php namespace Acioina\UserManagement\Console;

      use Acioina\UserManagement\Console\Commands\SessionClearCommand;
      use Acioina\UserManagement\Console\Commands\ExcelClearCommand;
      use Acioina\UserManagement\Console\Commands\ImportAllCommand;

      use Illuminate\Console\Scheduling\Schedule;
      use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


      class Kernel extends ConsoleKernel
      {
          /**
           * The Artisan commands provided by your application.
           *
           * @var array
           */
          protected $commands = [
              SessionClearCommand::class,
              ExcelClearCommand::class,
              ImportAllCommand::class,
          ];

          /**
           * Define the application's command schedule.
           *
           * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
           * @return void
           */
          protected function schedule(Schedule $schedule)
          {
              //$schedule->command('inspire')
              //         ->hourly();
          }

      }
