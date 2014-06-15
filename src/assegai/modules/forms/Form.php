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
            
            switch($field->getType()) {
                case 'text':
                if(!$field->getName()) {
                    $field->name($fieldname);
                }
                $buffer.= $renderer->text($field);
                break;
            }
        }
        
        return $buffer;
    }
}

