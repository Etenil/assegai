<?php

namespace assegai\modules\mail;

/**
 * Email service provider.
 */
interface Service
{
    public function send(Email $email);
}
