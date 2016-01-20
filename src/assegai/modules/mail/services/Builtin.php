<?php

namespace assegai\modules\mail\services;

class Builtin implements \assegai\modules\mail\Service
{
    public function send(\assegai\modules\mail\Email $email)
    {
        // Generating parameters;
        $params = array();
        if ($email->getSender()) {
            $params[] = "From: ".$email->getSender();
        }
        if ($email->getCc()) {
            $params[] = "Cc: ".$email->getCc();
        }
        if ($email->getBcc()) {
            $params[] = "Bcc: ".$email->getBcc();
        }
        if ($email->getReplyTo()) {
            $params[] = "Reply-To: ".$email->getReplyTo();
        }
        if ($email->getDate()) {
            $params[] = "Date: ".$email->getDate();
        }
        if ($email->getContentType()) {
            $params[] = "Content-Type: ".$email->getContentType();
        }

        // Now sending.
        return mail($email->getRecipient(), $email->getSubject(), $email->getBody(), implode("\r\n", $params));
    }
}
