<?php

namespace assegai\modules\forms\renderers;

use \assegai\modules\forms\fields;

interface IRenderer
{
    function text(fields\Field $field);
    function textarea(fields\Field $field);
    function select(fields\ChoiceField $field);
    function checkbox(fields\Field $field);
    function radio(fields\Field $field);
    function yesno(fields\Field $field);
    function time(fields\Field $field);
}
