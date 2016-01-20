<?php

namespace assegai\modules\forms\renderers;

use \assegai\modules\forms\fields;

interface IRenderer
{
    public function text(fields\Field $field);
    public function textarea(fields\Field $field);
    public function select(fields\ChoiceField $field);
    public function checkbox(fields\Field $field);
    public function radio(fields\Field $field);
    public function yesno(fields\Field $field);
    public function time(fields\Field $field);
}
