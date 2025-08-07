<?php

namespace App\Providers;
use App\Events\EventCompleted;
use App\Listeners\IssueCertificatesForCompletedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EventCompleted::class => [
            IssueCertificatesForCompletedEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
