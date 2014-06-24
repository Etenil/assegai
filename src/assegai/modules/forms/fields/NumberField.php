<?php

namespace assegai\modules\forms\fields;

class NumberField extends SizedField
{
    public $_min_value = null;
    public $_max_value = null;
    public $_decimal = false;
    public $_max_length = 10;

    function validate()
    {
        $errors = parent::validate();
        $validator = new Validator($this->_value);
        
        if($this->_decimal) {
            $validator->float(sprintf("%s must be a number", $this->getName()));
        }
        else {
            $validator->integer(sprintf("%s must be a number", $this->getName()));
        }

        if($this->_min_value) {
            $validator->min($this->_min_value, sprintf(
                "%s must be greater than %f",
                $this->getName(), $this->_min_value
            ));
        }
        if($this->_max_value) {
            $validator->max($this->_max_value, sprintf(
                "%s must be less than %f",
                $this->getName(), $this->_max_value
            ));
        }

        $this->_errors = array_merge($errors, $validator->allErrors());
        return $this->allErrors();
    }

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
    
    function decimal()
    {
        $this->_decimal = true;
    }
}

