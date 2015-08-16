<?php

namespace Assegai\Test\EventCore;

use Assegai\EventCore\EventListener;
use Assegai\EventCore\EventCore;
use Assegai\EventCore\Event;

class EventCoreTest extends PHPUnit_Framework_TestCase {
    protected $core;
    
    function triggerTest() {
        $$this->getMockBuilder('Assegai\\EventCore\\EventListener');
    }
}
