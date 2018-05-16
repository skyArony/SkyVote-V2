<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\DB\ActivityRecord;
use App\Models\DB\CandidateRecord;
use Illuminate\Support\Facades\Redis;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
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

        $schedule->call(function () {
            // 更新 pv 和 uv
            $activity_pv = Redis::zrange('activity_pv', 0, -1, 'WITHSCORES');
            $activity_uv = Redis::zrange('activity_uv', 0, -1, 'WITHSCORES');
            foreach ($activity_pv as $key => $value) {
                $activity_record = ActivityRecord::findOrFail($key);
                $activity_record->pv = $value;
                $activity_record->uv = $activity_uv[$key];
                $activity_record->save();
            }
            // 更新 ballot
            $activitys = Redis::KEYS('ballots:*');
            foreach ($activitys as $value) {
                $ballot = Redis::zrange($value, 0, -1, 'WITHSCORES');
                array_shift($ballot);
                foreach ($ballot as $key => $value2) {
                    $candidate_record = CandidateRecord::findOrFail($key);
                    $candidate_record->ballot = $value2;
                    $candidate_record->save();
                }
            }
        })->everyMinute();
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
