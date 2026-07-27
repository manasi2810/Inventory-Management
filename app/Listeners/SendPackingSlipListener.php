<?php

namespace App\Listeners;

use App\Events\DispatchCompleted;
use App\Jobs\ProcessDispatchDocumentsJob;
use Illuminate\Support\Facades\Log;

class ProcessDispatchDocumentsListener
{
    /**
     * Handle the event.
     */
    public function handle(DispatchCompleted $event): void
    {
        Log::info('DispatchCompleted Listener Executed. Dispatch ID: ' . $event->dispatch->id);

        ProcessDispatchDocumentsJob::dispatch($event->dispatch->id);
    }
}