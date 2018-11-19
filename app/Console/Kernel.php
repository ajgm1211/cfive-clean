<?php

namespace App\Console;

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
    Commands\UpdateCurrencies::class,
    Commands\UpdateCurrenciesEur::class,
  ];

  /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
  protected function schedule(Schedule $schedule)
  {
    // $schedule->command('inspire')
    //          ->hourly();
    $schedule->command('command:updateCurrenciesUsd')
      ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
    $schedule->command('command:updateCurrenciesEur')
      ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
    $schedule->exec('php /var/www/html/artisan queue:work --timeout=3600 --tries=7 &')->withoutOverlapping();
   // Comandos para backups 
    $schedule->command('backup:clean')->daily()->at('01:40');
    $schedule->command('backup:run')->daily()->at('02:00');
  }

  /**
     * Register the commands for the application.
     *
     * @return void
     */
  protected function commands()
  {
    $this->load(__DIR__.'/Commands');

    require base_path('routes/console.php');
  }
}
