<?php

namespace assegai\modules\forms\fields;

class TextField extends Field
{
    protected $_max_length = -1;
    protected $_multiline = false;

    function validate($data)
    {
        $errors = parent::validate($data);

        if($this->_max_length > -1 && strlen($data) > $this->_max_length) {
            $errors[] = sprintf(
                "%s is too long, it mustn't be longer than %d characters",
                $this->getName(), $this->_max_length
            );
        }

        return $errors;
    }

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

