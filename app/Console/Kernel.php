<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ReSuggestFollow;
use App\Console\Commands\ClearExpiredStory;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ReSuggestFollow::class,
        ClearExpiredStory::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('resuggest:follows')
            ->weekly()
            ->mondays()
            ->at('02:00')
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scheduling.log'));

        $schedule->command('clear:expiredstory')
            ->hourlyAt(15)
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/scheduling.log'));
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/New_York';
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
