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
