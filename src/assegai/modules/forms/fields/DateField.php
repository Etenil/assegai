<?php

namespace assegai\modules\forms\fields;

class DateField extends Field
{
    public $_min_value = null;
    public $_max_value = null;

    function minValue($val)
    {
        $this->_min_value = $val;
        return $this;
    }

    function maxValue($val)
    {
        $this->_max_value = $val;
        return $this;
    }
    
    function validate()
    {
        $errors = parent::validate();
        $validator = new Validator($this->_value);
        $validator->date(sprintf("%s is not a date", $this->getName()));
        
        if($this->_min_value) {
            $validator->minDate(sprintf("%s is too small", $this->getName()));
        }
        if($this->_max_value) {
            $validator->maxDate(sprintf("%s is too big", $this->getName()));
        }
        
        if($validator->hasErrors()) {
            $this->_errors = array_merge($errors, $validator->allErrors());
        }
        
        return $this->allErrors();
    }
}

