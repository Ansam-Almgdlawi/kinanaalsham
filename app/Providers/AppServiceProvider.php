<?php

namespace App\Providers;

use App\Repositories\EloquentOpportunityApplicationRepository;
use App\Repositories\EloquentOpportunityRepository;
use App\Repositories\OpportunityApplicationRepositoryInterface;
use App\Repositories\OpportunityRepositoryInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            OpportunityRepositoryInterface::class,
            EloquentOpportunityRepository::class
        );
        $this->app->bind(
            OpportunityApplicationRepositoryInterface::class,
            EloquentOpportunityApplicationRepository::class
        );


        $this->app->bind(
            \App\Repositories\TrainingCourseRepository::class,
            \App\Repositories\TrainingCourseRepository::class
        );

        $this->app->bind(
            \App\Services\TrainingCourseService::class,
            function ($app) {
                return new \App\Services\TrainingCourseService(
                    $app->make(\App\Repositories\TrainingCourseRepository::class)
                );
            }
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

    }
}
