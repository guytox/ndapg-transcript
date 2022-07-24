<?php

namespace App\Console;

use App\Jobs\ConfirmDevEmail;
use App\Models\User;
use App\Models\UserReferee;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Hash;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // for local development verify email
        if(config('app.env') === 'local')
        {
            $schedule->call(function () {
                $users = User::where('email_verified_at', null)->get();
                foreach ($users as $user) {
                    $user->email_verified_at = now();
                    $user->password = Hash::make('julipels');
                    $user->save();
                }
            })->everyMinute();
        }

        // this code deletes expired referee once expired runs every 30 minutes.
        $schedule->call(function () {
            $expiredReferee = UserReferee::where(['is_filled' => false])->where('expiry_date', '<', today())->get();
            foreach ($expiredReferee as $referee) {
                $referee->delete();
            }
        })->everyThirtyMinutes();
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
