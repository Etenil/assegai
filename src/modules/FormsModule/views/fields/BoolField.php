<?php

namespace etenil\assegai\modules\forms\fields;

class BoolField extends Field
{
    protected $_input_type = 'checkbox';

    function __construct($value = null)
    {
        parent::__construct($value);
        $this->_required = false;
    }
}
