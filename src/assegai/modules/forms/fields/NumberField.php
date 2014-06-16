<?php

namespace assegai\modules\forms\fields;

class NumberField extends Field
{
    public $_min_value = null;
    public $_max_value = null;

    function validate($data)
    {
        $errors = parent::validate($data);

        if(!is_numeric($data)) {
            $errors[] = sprintf(
                "%s must be a number",
                $this->getName()
            );
        }
        else {
            if($this->_min_value !== null && $data < $this->_min_value) {
                $errors[] = sprintf(
                    "%s must be greater than %f",
                    $this->getName(), $this->_min_value
                );
            }
            elseif($this->_max_value !== null && $data > $this->_max_value) {
                $errors[] = sprintf(
                    "%s must be less than %f",
                    $this->getName(), $this->_max_value
                );
            }
        }

        if($this->_max_length > -1 && strlen($data) > $this->_max_length) {
            $errors[] = sprintf(
                "%s is too long, it mustn't be longer than %d characters",
                $this->getName(), $this->_max_length
            );
        }

        return $errors;
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
}

