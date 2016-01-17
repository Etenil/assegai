<?php

class StatefulTest extends PHPUnit_Framework_TestCase
{
    public function testSetCookies()
    {
        $stateful = new \assegai\Stateful([], []);
        
        $stateful->setCookie('foo', 'bar');
        $this->assertEquals('bar', $stateful->getCookie('foo'));
        
        $stateful->setCookie('wibble', 'wobble', 100);
        $this->assertEquals('wobble', $stateful->getCookie('wibble'));
        
        $stateful->setCookie('toto', 'titi', 0);
        // Invalidated cookie should be instant, but wait 500ms little just to be sure.
        usleep(500);
        // The cookie must still be returned as nothing has been sent back to the user.
        $this->assertEquals('titi', $stateful->getCookie('toto'));
        
        $this->assertEquals(
            [
                'foo'    => 'bar',
                'wibble' => 'wobble',
                'toto'   => 'titi',
            ],
            $stateful->getAllCookies()
        );
    }
}
