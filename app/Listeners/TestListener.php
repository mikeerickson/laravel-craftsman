<?php

namespace TestListener;

use App\Events\MyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object MyEvent $event
     * @return void
     */
    public function handle(MyEvent $event)
    {
        //
    }
}
