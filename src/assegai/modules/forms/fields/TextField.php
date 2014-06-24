<?php

namespace assegai\modules\forms\fields;

class TextField extends SizedField
{
    protected $_max_length = null;
    protected $_multiline = false;

    function validate($data)
    {
        $errors = parent::validate($data);
        $validator = new Validator($this->_value);
        
        if(!$this->isMultiline()) {
            $validator->notRegexp("/\n/", sprintf("%s must be only one line", $this->getName()));
        }
        
        $this->_errors = array_merge($errors, $validator->allErrors());
        return $this->allErrors();
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

