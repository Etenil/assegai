<?php

namespace assegai\module\mail;

require(__DIR__.'/sdk.class.php');
require(__DIR__.'/ses.class.php');

class SesService implements Service
{
    function __construct($options)
    {
        CFRuntime::init($options['key'], $options['secret_key'],
                        $options['account_id'], $options['assoc_id']);
    }
    
    function send(Email $email)
    {
        $email = new AmazonSES();
        $dest = array();
        $opt = array();
        $msg = array();

        if($email->getRecipient()) $opt['ToAddresses'] = explode(';', $email->getRecipient());
        if($email->getCc()) $dest['CcAddresses'] = explode(';', $email->getCc());
        if($email->getBcc()) $dest['BccAddresses'] = explode(';', $email->getBcc());

        if($email->getReplyTo()) $opt['ReplyToAddresses'] = explode(';', $email->getReplyTo());

        $msg['Subject.Data'] = $email->getSubject();
        $msg['Body.Text.Data'] = $email->getBody();
        
        return $email->send_email($email->getSender(), $dest, $msg, $opt);
    }
}

?>