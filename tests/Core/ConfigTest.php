<?php

use Assegai\Core\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {
    protected $conf = null;
    
    function setUp() {
        $this->conf = new Config();
        $this->conf->loadArray(array(
            'blah',
            'foo' => 'qux',
            'yadda' => array('yadda'),
            'bar' => array(
                'fiz' => 'buz',
                'obi' => array(
                    'wan' => 'kenobi'
                )
            )
        ));
    }
    
    function testLoad() {
        $conf = new Config();
        $conf->loadArray(array('bar' => 'baz'));
        
        $this->assertEquals('baz', $conf->get('bar'));
    }
    
    function testValueGet() {
        $this->assertEquals(true, $this->conf->get('blah'));
    }
    
    function testKeyGet() {
        $this->assertEquals('qux', $this->conf->get('foo'));
    }
    
    function testArrayGet() {
        $this->assertEquals(array('yadda'), $this->conf->get('yadda'));
    }
    
    function testTraverseGet() {
        $this->assertEquals('buz', $this->conf->get('bar.fiz'));
        $this->assertEquals('kenobi', $this->conf->get('bar.obi.wan'));
    }
}
