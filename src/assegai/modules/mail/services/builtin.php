<?php

namespace assegai\modules\mail\services;

use assegai\modules\mail;

class BuiltinService implements Service
{
    function send(Email $email)
    {
        // Generating parameters;
        $params = array();
        if($email->getSender()) $params[] = "From: ".$email->getSender();
        if($email->getCc()) $params[] = "Cc: ".$email->getCc();
        if($email->getBcc()) $params[] = "Bcc: ".$email->getBcc();
        if($email->getReplyTo()) $params[] = "Reply-To: ".$email->getReplyTo();
        if($email->getDate()) $params[] = "Date: ".$email->getDate();
        if($email->getContentType()) $params[] = "Content-Type: ".$email->getContentType();

        // Now sending.
        return mail($email->getRecipient(), $email->getSubject(), $email->getBody(), implode("\n\r", $params));
    }
}

?>