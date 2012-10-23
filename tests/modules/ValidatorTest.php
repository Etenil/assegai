<?php

require('lib/loader.php');
require('lib/modules/paginator/paginator.php');

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $v = new Module_Validator();
    }
}
