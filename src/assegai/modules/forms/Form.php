<?php

namespace assegai\modules\forms;

use \assegai\modules\forms\fields;

class Form
{
    protected $_fields = array();
    protected $_errors = array();

    public function __get($name)
    {
        if(isset($this->_fields, $name)) {
            return $this->_fields[$name];
        }
    }

    protected function field($name, $type)
    {
        $fieldname = 'assegai\modules\forms\fields\\' . $type . 'Field';
        $field = new $fieldname;
        $this->_fields[$name] = $field;

        return $field;
    }
    
    public function render(renderers\IRenderer $renderer)
    {
        $buffer = '';
        foreach($this->_fields as $fieldname => $field) {
            /* The field cannot know about the variable name it was given,
               unfortunately. So we inform it about its name here, unless
               the developer manually set a different name for it. */
            if(!$field->getName()) {
                $field->name($fieldname);
            }
            
            switch($field->getType()) {
                case 'text':
                    if($field->isMultiline()) {
                        $buffer.= $renderer->textarea($field);
                    }
                    else {
                        $buffer.= $renderer->text($field);
                    }
                    break;
                case 'choice':
                    $buffer.= $renderer->select($field);
                    break;
                case 'bool':
                    $buffer.= $renderer->checkbox($field);
                    break;
                default:
                    throw new \Exception(sprintf("Don't know how to render field type '%s'", get_class($field)));
            }
        }
        
        return $buffer;
    }

    public function isValid(array $data)
    {
        foreach($this->_fields as $fieldname => $field) {
            $r = new \ReflectionClass($field);
            if(!$r->isSubclassOf('assegai\modules\forms\fields\Field')) {
                continue;
            }

            if(!$field->getName()) {
                $field->name($fieldname);
            }

            $this->_errors = array_merge($this->_errors, $field->validate($data[$field->getName()]));
        }

        return (count($this->_errors) == 0);
    }
}

