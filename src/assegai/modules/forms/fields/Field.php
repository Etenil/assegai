<?php

namespace assegai\modules\forms\fields;

class Field
{
    protected $_name = '';
    protected $_blank = true;
    protected $_default = null;
    protected $_editable = true;
    protected $_help = null;
    protected $_label = '';
    protected $_value = null;

    function validate($data)
    {
        $errors = [];
        if(!$data && !$this->_blank) {
            $errors[] = sprintf("%s cannot be blank", $this->getName());
        }

        return $errors;
    }

    function name($val)
    {
        $this->_name = $val;
        return $this;
    }

    function blank($val)
    {
        $this->_blank = (bool)$val;
        return $this;
    }

    function defaults($val) 
    {
        $this->_default = $val;
        return $this;
    }

    function editable($val) 
    {
        $this->_editable = (bool)$val;
        return $this;
    }

    function help($val) 
    {
        $this->_help = $val;
        return $this;
    }
    
    function value($val)
    {
        $this->_value = $val;
        return $this;
    }
    
    /**
     * This essentially returns the field's class name without the "Field" part.
     */
    function getType()
    {
        $myclass = get_class($this);
        return strtolower(substr($myclass, strrpos($myclass, '\\') + 1, -5));
    }
    
    function getName()
    {
        return $this->_name;
    }
    
    function getLabel()
    {
        if($this->_label) {
            return $this->_label;
        }
        else {
            return ucwords($this->getName());
        }
    }
    
    function getValue()
    {
        return $this->_value;
    }
    
    public function getDefault()
    {
        return $this->_default;
    }
    
    public function isEditable()
    {
        return $this->_editable;
    }
    
    public function getHelp()
    {
        return $this->_help;
    }
}

