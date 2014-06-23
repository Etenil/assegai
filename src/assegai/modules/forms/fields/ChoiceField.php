<?php

namespace assegai\modules\forms\fields;

class ChoiceField extends Field
{
    protected $_choices = array();
    protected $_multiple = false;

    function validate($data)
    {
        $errors = parent::validate($data);

        if(is_array($data) && !$this->isMultiple()) {
            $errors[] = sprintf(
                "only choose one value for %s",
                $this->getName(), $this->_max_length
            );
        }
        elseif(is_array($data) && $this->isMultiple()) {
            $choices = $this->_choices;
            $errors = array_reduce(
                $data,
                function($carry, $item) use($choices) {
                    if(!in_array($item, $choices)) {
                        $carry[] = sprintf("'%s' is an unknown choice for %s", $item, $this->getName());
                    }
                },
                $errors
            );
        }
        elseif(!in_array($item, $this->_choices)) {
            $carry[] = sprintf("'%s' is an unknown choice for %s", $item, $this->getName());
        }

        return $errors;
    }
    
    public function choices(array $val) 
    {
        $this->_choices = $val;
        return $this;
    }

    public function getChoices()
    {
        return $this->_choices;
    }

    public function multiple($val)
    {
        $this->_multiple = (bool)$val;
        return $this;
    }

    public function isMultiple()
    {
        return $this->_multiple;
    }
}

