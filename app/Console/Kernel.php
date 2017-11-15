<?php

namespace App\Console;

use App\Console\Commands\PruneSubIdxFiles;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\GenerateSitemap::class,
        Commands\CollectMeta::class,
        Commands\CleanTemporaryStuff::class,
        Commands\CalculateDiskUsage::class,
        Commands\CleanDisk::class,
        Commands\PruneSubIdxFiles::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->dailyAt('2:00');

        $schedule->command('st:prune-sub-idx-files')->dailyAt('2:15');

        $schedule->command('st:clean-temporary-stuff')->dailyAt('2:30');

        $schedule->command('st:collect-meta')->everyTenMinutes();

        $schedule->command('st:calculate-disk-usage')->everyTenMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
