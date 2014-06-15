<?php

namespace assegai\modules\forms\renderers;

use \assegai\modules\forms\fields;

abstract class Renderer implements IRenderer
{
    /**
     * Super simple templating function, allows clean definition of fields.
     * This implements dumb mustache-like placeholders like so: {{placeholder}}.
     */
    protected function tpl($template, array $values)
    {
        $buffer = $template;
        foreach($values as $key => $val) {
            $buffer = str_replace('{{' . $key . '}}', $val, $buffer);
        }
        
        // Cleaning up the remaining placeholders.
        $buffer = preg_replace('%\{\{[^}]*\}\}%', '', $buffer);
        
        return $buffer;
    }
}
