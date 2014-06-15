<?php

namespace assegai\modules\forms\fields;

class TextField extends Field
{
    public $_max_length = -1;

    function maxLength($val)
    {
        $this->_max_length = $val;
        return $this;
    }
    
    function getMaxLength()
    {
        return $this->_max_length;
    }
}

