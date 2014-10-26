<?php

namespace assegai\events;

class HttpEvent extends Event implements IEvent
{
    protected $type = 'http';
    
    function loadGlobals()
    {
        // Do stuff.
        return $this;
    }
}
