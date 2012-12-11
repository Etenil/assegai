<?php

namespace assegai\module\mail

/**
 * Email service provider.
 */
interface Service
{
    function send(Email $email);
}
