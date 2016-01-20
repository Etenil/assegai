<?php

namespace assegai\modules\forms;

use \assegai\modules\forms\fields;

class Form
{
    protected $modules;
    protected $_fields = array();
    protected $_errors = array();

    public function __get($name)
    {
        if (isset($this->_fields, $name)) {
            return $this->_fields[$name];
        }
    }

    public function __construct($modules, array $data = array())
    {
        $this->modules = $modules;

        if ($data) {
            $this->populateFields($data);
        }
    }

    protected function field($name, $type)
    {
        $fieldname = 'assegai\modules\forms\fields\\' . $type . 'Field';
        $field = new $fieldname;
        $field->name($name);
        $this->_fields[$name] = $field;

        return $field;
    }

    public function render(renderers\IRenderer $renderer)
    {
        $buffer = '';
        foreach ($this->_fields as $fieldname => $field) {
            switch ($field->getInputType()) {
                case 'input':
                    if (method_exists($field, 'isMultiline') && $field->isMultiline()) {
                        $buffer.= $renderer->textarea($field);
                    } else {
                        $buffer.= $renderer->text($field);
                    }
                    break;
                case 'select':
                    $buffer.= $renderer->select($field);
                    break;
                case 'checkbox':
                    $buffer.= $renderer->checkbox($field);
                    break;
                case 'radio':
                    $buffer.= $renderer->radio($field);
                    break;
                default:
                    throw new \Exception(sprintf(
                        "Don't know how to render field type '%s'",
                        get_class($field)
                    ));
            }
        }

        return $buffer;
    }

    public function populateFields(array $data)
    {
        foreach ($this->_fields as $fieldname => $field) {
            if (isset($data[$field->getName()])) {
                $field->value($data[$field->getName()]);
            }
        }
    }

    public function isValid(array $data)
    {
        foreach ($this->_fields as $fieldname => $field) {
            $r = new \ReflectionClass($field);
            if (!$r->isSubclassOf('assegai\modules\forms\fields\Field')) {
                continue;
            }

            if (!$field->getName()) {
                $field->name($fieldname);
            }

            $field->value($data[$field->getName()]);
            $this->_errors = array_merge($this->_errors, $field->validate());
        }

        return !$this->hasErrors();
    }

    public function hasErrors()
    {
        return count($this->_errors) > 0;
    }

    public function allErrors()
    {
        return $this->_errors;
    }
}
