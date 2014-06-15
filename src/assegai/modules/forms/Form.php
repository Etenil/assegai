<?php

namespace assegai\modules\forms;

use \assegai\modules\forms\fields;

class Form
{
    protected function field($type)
    {
        $fieldname = 'assegai\modules\forms\fields\\' . $type . 'Field';
        return new $fieldname;
    }
    
    public function render(renderers\IRenderer $renderer)
    {
        $buffer = '';
        foreach($this as $fieldname => $field) {
            $r = new \ReflectionClass($field);
            if(!$r->isSubclassOf('assegai\modules\forms\fields\Field')) {
                continue;
            }
            
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
}

