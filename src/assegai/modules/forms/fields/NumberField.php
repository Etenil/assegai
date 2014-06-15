<?php

namespace assegai\modules\forms\fields;

class NumberField extends Field
{
    public $_min_value = null;
    public $_max_value = null;

    function minValue($val)
    {
        $this->_min_value = $val;
        return $this;
    }

    function maxValue($val)
    {
        $this->_max_value = $val;
        return $this;
    }
}

