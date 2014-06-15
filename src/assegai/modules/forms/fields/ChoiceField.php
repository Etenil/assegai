<?php

namespace assegai\modules\forms\fields;

class ChoiceField extends Field
{
    protected $_choices = array();
    
    function choices(array $val) 
    {
        $this->_choices = $val;
        return $this;
    }
    
    public function getChoices()
    {
        return $this->_choices;
    }
}

