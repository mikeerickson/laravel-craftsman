<?php

namespace MyListener;

use App\Events\MyEvent;

class MyListener 
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
