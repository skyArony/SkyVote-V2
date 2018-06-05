<?php

namespace App\Providers;

use App\Models\DB\Activity;
use App\Models\DB\Candidate;
use App\Observers\ActivityObserver;
use App\Observers\CandidateObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 监听 Activity 模型的事件
        Activity::observe(ActivityObserver::class);
        // 监听 Candidate 模型的事件
        Candidate::observe(CandidateObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
