<?php

namespace etenil\assegai\modules\forms\renderers;

use \etenil\assegai\modules\forms\fields;

class Paragraph extends Renderer implements IRenderer
{
    protected $errors;

    const TPL_FIELD = '
        <p id="field-{{name}}" class="{{class}}">
            <label for="{{id}}">{{label}}:</label>
            {{input}}
            {{help}}
        </p>
    ';
    const TPL_HELPTEXT = '<span class="helptext">{{help}}</span>';
    const TPL_PREFIELD = '
        <p id="field-{{name}}">
            {{input}}
            <label for="{{id}}">{{label}}</label>
            {{help}}
        </p>
    ';
    const TPL_INPUT = '
        <input type="{{type}}" name="{{name}}" id="{{id}}" class="{{class}}" value="{{value}}" {{extra}} />
    ';
    const TPL_TEXTAREA = '<textarea name="{{name}}" id="{{id}}" class="{{class}}" {{extra}}>{{value}}</textarea>';
    const TPL_SELECT = '<select name="{{name}}" id="{{id}}" class="{{class}}" {{extra}}>{{options}}</select>';
    const TPL_SELECT_OPTION = '<option value="{{value}}" {{selected}}>{{label}}</option>';

    public function __construct(array $errors = array())
    {
        $this->errors = $errors;
    }
    
    protected function field($field, $input, $prefield = false)
    {
        if($field->hasErrors()) {
            $field->addClass('error');
        }

        $id = $field->getId() ?: sprintf('input-%s', $field->getName());

        $tpl = self::TPL_FIELD;
        if($prefield) {
            $tpl = self::TPL_PREFIELD;
        }
        $help = '';
        if($field->getHelp()) {
            $help = $this->tpl(self::TPL_HELPTEXT, array(
                'name' => $field->getName(),
                'id' => $id,
                'help' => $field->getHelp(),
            ));
        }
        return $this->tpl($tpl, array(
            'id' => $id,
            'name' => $field->getName(),
            'label' => $field->getLabel(),
            'input' => $input,
            'help' => $help,
            'class' => implode(' ', $field->getClasses()),
        ));
    }
    
    function text(fields\Field $field)
    {
        return $this->field(
            $field,
            $this->tpl(
                self::TPL_INPUT,
                array(
                    'name' => $field->getName(),
                    'value' => $field->getValue(),
                    'type' => $field->getType(),
                )
            )
        );
    }
    
    function textarea(fields\Field $field)
    {
        return $this->field(
            $field,
            $this->tpl(
                self::TPL_TEXTAREA,
                array(
                    'name' => $field->getName(),
                    'value' => $field->getValue(),
                )
            )
        );
    }
    
    function select(fields\ChoiceField $field)
    {
        $options = '';
        foreach($field->getChoices() as $choice_lbl => $choice_val) {
            $options.= $this->tpl(
                self::TPL_SELECT_OPTION,
                array(
                    'value' => $choice_val,
                    'label' => is_int($choice_lbl) ? $choice_val : $choice_lbl,
                    'selected' => $field->getValue() == $choice_val ? 'selected' : '',
                )
            );
        }
        
        return $this->field(
            $field,
            $this->tpl(
                self::TPL_SELECT,
                array(
                    'name' => $field->getName(),
                    'options' => $options,
                )
            )
        );
    }
    
    function checkbox(fields\Field $field)
    {
        return $this->field(
            $field,
            $this->tpl(
                self::TPL_INPUT,
                array(
                    'type' => 'checkbox',
                    'name' => $field->getName(),
                    'extra' => $field->getValue() !== null ? 'checked' : '',
                )
            ),
            true
        );
    }
    
    function radio(fields\Field $field)
    {
        $options = '';
        $iter = 0;
        foreach($field->getChoices() as $choice_lbl => $choice_val) {
            $field->setLabel($choice_val)
                  ->setId(sprintf('radio-%s-%d', $field->getName(), $iter++));
            $options.= $this->field(
                $field,
                $this->tpl(
                    self::TPL_INPUT,
                    array(
                        'type' => 'radio',
                        'name' => $field->getName(),
                        'id' => $field->getid(),
                        'extra' => $field->getValue() == $choice_val ? 'selected' : '',
                    )
                ),
                true
            );
        }
        
        return $options;
    }
    
    function yesno(fields\Field $field)
    {
    }
    
    function time(fields\Field $field)
    {
    }
}

