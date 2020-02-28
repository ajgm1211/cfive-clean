<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendQuotesJob;
use App\Jobs\ProcessExpiredContractsJob;
use App\Jobs\UpdateCurrenciesJob;
use App\Jobs\UpdateCurrenciesEurJob;

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
        Commands\SendQuotes::class,
        Commands\ProcessExpiredContracts::class,
        'Laravel\Passport\Console\ClientCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->job(new SendQuotesJob)->cron('*/3 * * * *')->appendOutputTo(storage_path('logs/commands.log'));
        /*$schedule->job(new ProcessExpiredContractsJob)->dailyAt('00:00')->appendOutputTo(storage_path('logs/commands.log'));
        $schedule->job(new UpdateCurrenciesJob)->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
        $schedule->job(new UpdateCurrenciesEurJob)->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));*/

        $schedule->command('command:updateCurrenciesUsd')
            ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
        $schedule->command('command:updateCurrenciesEur')
            ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
        $schedule->command('command:sendQuotes')
            ->cron('*/3 * * * *')->appendOutputTo(storage_path('logs/commands.log'));
        $schedule->command('command:processExpiredContracts')
            ->dailyAt('00:00')->appendOutputTo(storage_path('logs/commands.log'));
        //$schedule->exec('php /var/www/html/artisan queue:work --timeout=3600 --tries=7 &')
        //    ->withoutOverlapping()->appendOutputTo(storage_path('logs/commands.log'));
        //$schedule->exec('php /var/www/html/artisan queue:work --queue=importation --timeout=3600 --tries=7 &')
        //->withoutOverlapping()->appendOutputTo(storage_path('logs/commands.log'));

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
