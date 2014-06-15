<?php

namespace assegai\modules\forms\renderers;

use \assegai\modules\forms\fields;

class Paragraph extends Renderer implements IRenderer
{
    protected $errors;

    const TPL_FIELD = '
        <p id="field-{{name}}">
            <label>{{label}}</label>
            {{input}}
        </p>
    ';
    const TPL_INPUT = '
        <input type="{{type}}" name="{{name}}" id="input-{{name}}" class="{{class}}" {{extra}} />
    ';

    public function __construct(array $errors = array())
    {
        $this->errors = $errors;
    }
    
    function text(fields\Field $field)
    {
        $input = $this->tpl(self::TPL_INPUT, array(
            'name' => $field->getName(),
            'type' => 'text',
        ));
        
        $field = $this->tpl(self::TPL_FIELD, array(
            'name' => $field->getName(),
            'label' => $field->getLabel(),
            'input' => $input,
        ));
        
        return $field;
    }
    
    function textarea(fields\Field $field)
    {
    }
    
    function select(fields\Field $field)
    {
    }
    
    function checkbox(fields\Field $field)
    {
    }
    
    function checkboxes(fields\Field $field)
    {
    }
    
    function yesno(fields\Field $field)
    {
    }
    
    function time(fields\Field $field)
    {
    }
    
    function input(fields\Field $field)
    {
    }
}

