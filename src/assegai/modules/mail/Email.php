<?php

namespace assegai\modules\mail;

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
    public function getRecipient()
    {
        return $this->_recipient;
    }
    
    public function setRecipient($val)
    {
        $this->_recipient = $val;
        return $this;
    }
    
    public function getSender()
    {
        return $this->_sender;
    }
    
    public function setSender($val)
    {
        $this->_sender = $val;
        return $this;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    public function setBody($val)
    {
        $this->_body = $val;
        return $this;
    }
    
    public function getCc()
    {
        return $this->_cc;
    }
    
    public function setCc($val)
    {
        $this->_cc = $val;
        return $this;
    }
    
    public function getBcc()
    {
        return $this->_bcc;
    }
    
    public function setBcc($val)
    {
        $this->_bcc = $val;
        return $this;
    }
    
    public function getReplyTo()
    {
        return $this->_reply_to;
    }
    
    public function setReplyTo($val)
    {
        $this->_reply_to = $val;
        return $this;
    }
    
    public function getSubject()
    {
        return $this->_subject;
    }
    
    public function setSubject($val)
    {
        $this->_subject = $val;
        return $this;
    }
    
    public function getDate()
    {
        return $this->_date;
    }
    
    public function setDate($val)
    {
        $this->_date = $val;
        return $this;
    }
    
    public function getContentType()
    {
        return $this->_content_type;
    }
    
    public function setContentType($val)
    {
        $this->_content_type = $val;
        return $this;
    }
}
