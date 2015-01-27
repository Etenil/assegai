<?php

namespace etenil\assegai\modules\forms\fields;

class SizedField extends Field
{
    public $_max_length = -1;
    protected $_input_type = 'input';
    protected $_type = 'text';
    
    function validate()
    {
        $errors = parent::validate();
        $validator = new Validator($this->_value);
        
        if($this->_max_length > -1) {
            $validator->maxLength($this->_max_length, sprintf(
                "%s must not exceed %d characters",
                $this->getName(), $this->_max_length
            ));
        }
        
        $this->_errors = array_merge($errors, $validator->allErrors());
        return $this->allErrors();
    }
    
    function maxLength($len)
    {
        $this->_max_length = $len;
        return $this;
    }
}

