<?php

namespace App\Events;

use App\Models\Dispatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class DispatchCompleted implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public $dispatch;

    public function __construct(Dispatch $dispatch)
    {
        $this->dispatch = $dispatch;
    }
}