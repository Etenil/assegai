<?php

namespace assegai\modules\mail
{
    /**
     * Email service provider.
     */
    interface Service
    {
        function send(Email $email);
    }
}
