<?php

namespace assegai\modules\forms\fields;

class TextField extends Field
{
    protected $_max_length = -1;
    protected $_multiline = false;

    function maxLength($val)
    {
        $this->_max_length = $val;
        return $this;
    }
    
    function getMaxLength()
    {
        return $this->_max_length;
    }
    
    function multiline($val)
    {
        $this->_multiline = (bool)$val;
        return $this;
    }
    
    function isMultiline()
    {
        return $this->_multiline;
    }
}

