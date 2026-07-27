<?php

namespace App\Providers;

use App\Events\DispatchCompleted;
use App\Listeners\ProcessDispatchDocumentsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DispatchCompleted::class => [
            ProcessDispatchDocumentsListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}