<?php

namespace assegai\module\mail

/**
 * An email object.
 */
class Email
{
    protected $_recipient;
    protected $_sender;
    protected $_body;
    protected $_cc;
    protected $_bcc;
    protected $_reply_to;
    protected $_subject;
    protected $_date;
    protected $_content_type;

// Accessors.
    function getRecipient()
    {
        return $this->_recipient;
    }

    function setRecipient($val)
    {
        $this->_recipient = $val;
        return $this;
    }

    function getSender()
    {
        return $this->_sender;
    }

    function setSender($val)
    {
        $this->_sender = $val;
        return $this;
    }

    function getBody()
    {
        return $this->_body;
    }

    function setBody($val)
    {
        $this->_body = $val;
        return $this;
    }

    function getCc()
    {
        return $this->_cc;
    }

    function setCc($val)
    {
        $this->_cc = $val;
        return $this;
    }

    function getBcc()
    {
        return $this->_bcc;
    }

    function setBcc($val)
    {
        $this->_bcc = $val;
        return $this;
    }

    function getReplyTo()
    {
        return $this->_reply_to;
    }

    function setReplyTo($val)
    {
        $this->_reply_to = $val;
        return $this;
    }

    function getSubject()
    {
        return $this->_subject;
    }

    function setSubject($val)
    {
        $this->_subject = $val;
        return $this;
    }

    function getDate()
    {
        return $this->_date;
    }

    function setDate($val)
    {
        $this->_date = $val;
        return $this;
    }

    function getContentType()
    {
        return $this->_content_type;
    }

    function setContentType($val)
    {
        $this->_content_type = $val;
        return $this;
    }

}
