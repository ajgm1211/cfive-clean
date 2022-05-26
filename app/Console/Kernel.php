<?php

namespace App\Console;

use App\Jobs\SaveFclRatesByContractJob;
use App\Jobs\SyncCompaniesVforwarding;
use App\Jobs\SyncCompaniesVisualtrans;
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
        Commands\SendQuotes::class,
        Commands\ProcessExpiredContracts::class,
        Commands\mappingMaerskCode::class,
        Commands\JoinVariationHarbor::class,
        Commands\UpdateStatusInland::class,
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
        //Jobs
        $schedule->job(new SyncCompaniesVforwarding, 'high')->cron('* * * * *')->appendOutputTo(storage_path('logs/laravel.log'));
        //$schedule->job(new SyncCompaniesVisualtrans)->cron('0 */4 * * *')->appendOutputTo(storage_path('logs/commands.log'));
        //$schedule->job(new SaveFclRatesByContractJob)->cron('0 */8 * * *')->appendOutputTo(storage_path('logs/commands.log'));

        //Commands
        /*$schedule->command('command:updateCurrenciesUsd')
            ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));*/
         //$schedule->command('command:updateCurrenciesEur')
          //   ->twiceDaily(6, 14)->appendOutputTo(storage_path('logs/commands.log'));
        //$schedule->command('command:sendQuotes')
          //  ->cron('*/3 * * * *')->appendOutputTo(storage_path('logs/commands.log'));
         //$schedule->command('command:processExpiredContracts')
         //    ->dailyAt('23:50')->appendOutputTo(storage_path('logs/commands.log'));
         //$schedule->command('command:UpdateStatusInland')->cron('0 */4 * * *')->appendOutputTo(storage_path('logs/commands.log'));
        // $schedule->command('command:generateQuotePdf')
          //   ->dailyAt('00:30')
         //    ->withoutOverlapping()
          //   ->appendOutputTo(storage_path('logs/commands.log'));

        // Backups
         //$schedule->command('backup:clean')->daily()->at('01:40');
        // $schedule->command('backup:run')->daily()->at('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
