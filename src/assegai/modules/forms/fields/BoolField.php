<?php

namespace assegai\modules\forms\fields;

class BoolField extends Field
{
    function __construct($value = null)
    {
        parent::__construct($value);
        $this->_required = false;
    }
}
