<?php

namespace etenil\assegai\modules\forms\renderers;

use \etenil\assegai\modules\forms\fields;

abstract class Renderer implements IRenderer
{
    protected function guessId($field)
    {
        return $field->getId() ?: sprintf('input-%s', $field->getName());
    }
    
    /**
     * Super simple templating function, allows clean definition of fields.
     * This implements dumb mustache-like placeholders like so: {{placeholder}}.
     */
    protected function tpl($template, array $values)
    {
        if((!isset($values['id']) || !$values['id']) && isset($values['name'])) {
            $values['id'] = sprintf('input-%s', $values['name']);
        }

        $buffer = $template;
        foreach($values as $key => $val) {
            $buffer = str_replace('{{' . $key . '}}', $val, $buffer);
        }
        
        // Cleaning up the remaining placeholders.
        $buffer = preg_replace('%\{\{[^}]*\}\}%', '', $buffer);
        
        return $buffer;
    }
}
