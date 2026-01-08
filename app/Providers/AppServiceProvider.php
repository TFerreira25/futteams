<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Services & Repositories
        $this->app->singleton('PlayerService', function ($app) {
            return new \App\Services\PlayerService(
                new \App\Repositories\PlayerRepository()
            );
        });

        $this->app->singleton('GameService', function ($app) {
            return new \App\Services\GameService(
                new \App\Repositories\GameRepository(),
                new \App\Services\TeamGenerationService(
                    new \App\Repositories\GameRepository(),
                    new \App\Repositories\PlayerRepository(),
                    new \App\Repositories\TeamRepository()
                )
            );
        });

        $this->app->singleton('ResultService', function ($app) {
            return new \App\Services\ResultService(
                new \App\Repositories\StatisticRepository()
            );
        });

        $this->app->singleton('RankingService', function ($app) {
            return new \App\Services\RankingService(
                new \App\Repositories\RankingRepository()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
