<?php

namespace assegai\modules\forms\fields;

class RadioField extends Field
{
    protected $_choices = array();
    protected $_input_type = 'radio';

    public function validate()
    {
        $errors = parent::validate();
        $validator = new Validator($this->_value);

        if (is_array($data) && !$this->isMultiple()) {
            $errors[] = sprintf(
                "only choose one value for %s",
                $this->getName()
            );
        } elseif (is_array($data) && $this->isMultiple()) {
            foreach ($data as $item) {
                $validator->setValue($item);
                $validator->oneOf($this->getChoices(), sprintf("'%s' is an unknown choice for %s", $item, $this->getName()));
            }
        } else {
            $validator->oneOf($this->getChoices(), sprintf("'%s' is an unknown choice for %s", $item, $this->getName()));
        }
        
        if ($validator->hasErrors()) {
            $this->_errors = array_merge($errors, $validator->allErrors());
        }

        return $this->allErrors();
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
