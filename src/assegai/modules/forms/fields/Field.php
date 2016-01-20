<?php

namespace assegai\modules\forms\fields;

class Field
{
    protected $_name = '';
    protected $_id = null;
    protected $_required = true;
    protected $_default = null;
    protected $_editable = true;
    protected $_help = null;
    protected $_label = '';
    protected $_value = null;
    // Fields are invalid by default because they're blank and are required.
    protected $_errors = array();

    //! Input type. Used for rendering.
    protected $_input_type = null;
    //! Field type. Will automatically guess from the class name if left to null.
    protected $_type = null;

    protected $_classes = array();

    public function __construct($value = null)
    {
        $this->_value = $value;
    }

    public function validate()
    {
        $validator = new Validator($this->_value);

        if ($this->isRequired()) {
            $validator->required(sprintf("%s cannot be blank", $this->getName()));
        }

        $this->_errors = $validator->allErrors();
        return $this->allErrors();
    }

    public function name($val)
    {
        $this->_name = $val;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }
    public function setId($val)
    {
        $this->_id = $val;
        return $this;
    }

    public function required($val)
    {
        $this->_required = (bool)$val;
        return $this;
    }

    public function defaults($val)
    {
        $this->_default = $val;
        return $this;
    }

    public function editable($val)
    {
        $this->_editable = (bool)$val;
        return $this;
    }

    public function help($val)
    {
        $this->_help = $val;
        return $this;
    }

    public function value($val)
    {
        $this->_value = $val;
        return $this;
    }

    public function addClass($myclass)
    {
        $this->_classes[$myclass]++;
        return $this;
    }
    public function delClass($myclass)
    {
        unset($this->_classes[$myclass]);
        return $this;
    }
    public function clearClasses()
    {
        $this->_classes = array();
        return $this;
    }
    public function getClasses()
    {
        return array_keys($this->_classes);
    }

    /**
     * This essentially returns the field's class name without the "Field" part.
     */
    public function getType()
    {
        if ($this->_type) {
            return $this->_type;
        } else {
            $myclass = get_class($this);
            return strtolower(substr($myclass, strrpos($myclass, '\\') + 1, -5));
        }
    }

    public function getInputType()
    {
        return $this->_input_type;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setLabel($val)
    {
        $this->_label = $val;
        return $this;
    }

    public function getLabel()
    {
        if ($this->_label) {
            return $this->_label;
        } else {
            return ucwords($this->getName());
        }
    }

    public function isRequired()
    {
        return $this->_required;
    }

    public function getDefault()
    {
        return $this->_default;
    }

    public function getValue()
    {
        if ($this->_value !== null) {
            return $this->_value;
        } else {
            return $this->getDefault();
        }
    }

    public function isEditable()
    {
        return $this->_editable;
    }

    public function getHelp()
    {
        return $this->_help;
    }

    public function hasErrors()
    {
        return count($this->_errors) > 0;
    }

    public function isValid()
    {
        return !$this->hasErrors();
    }

    public function allErrors()
    {
        return $this->_errors;
    }
}
