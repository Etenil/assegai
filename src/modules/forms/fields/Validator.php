<?php

namespace assegai\modules\forms\fields;

/**
 * Field validator.
 */
class Validator
{
    protected $_value;
    protected $_errors = array();
    
    public function __construct($value)
    {
        $this->setValue($value);
    }
    
    public function hasErrors()
    {
        return count($this->_errors) > 0;
    }
    
    public function allErrors()
    {
        return $this->_errors;
    }
    
    public function getValue()
    {
        return $this->_value;
    }
    
    public function setValue($val)
    {
        $this->_value = $val;
        return $this;
    }
    
    /**
     * The field is required. This will trigger an error if the field is left blank.
     * @param $msg string the error message.
     */
    public function required($msg)
    {
        if(!$this->_value) {
            $this->_errors[] = $msg;
        }
        
        return $this;
    }
    
    /**
     * Checks the that the input value is an email. Will perform format and DNS checks.
     * @param $msg string the error message.
     */
    public function email($msg)
    {
        $email = $this->_value;
        
        if(!$email) {
            return true;
        }
        
        $isValid = true;
        $atIndex = strrpos($email, '@');
        if(is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        }
        else {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                $isValid = false;
            }
            else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            }
            else if ($local[0] == '.' || $local[$localLen-1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            }
            else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            }
            else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
                    $isValid = false;
                }
            }
            // check DNS
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                $isValid = false;
            }
        }
        
        if(!$isValid) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is a floating point number.
     * @param $msg string the error message.
     */
    public function float($msg)
    {
        if(filter_var($this->_value, FILTER_VALIDATE_FLOAT) === FALSE) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is an integer number.
     * @param $msg string the error message.
     */
    public function integer($msg)
    {
        if(filter_var($this->_value, FILTER_VALIDATE_INT) === FALSE) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value only contains digits.
     * @param $msg string the error message.
     */
    public function digits($msg)
    {
        if(strlen($this->_value) > 0 && !ctype_digit((string) $this->_value)) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value has a numerical value of at least $limit.
     * @param $limit float is the minimum value possible (inclusive).
     * @param $msg string the error message.
     */
    public function min($limit, $msg)
    {
        if(strlen($this->_value) > 0 && (float)$this->_value < (float)$limit) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value has a numerical value of at most $limit.
     * @param $limit float is the maximum value possible (inclusive).
     * @param $msg string the error message.
     */
    public function max($limit, $msg)
    {
        if(strlen($this->_value) > 0 && (float)$this->_value > (float)$limit) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is a numerical value between the two bounds
     * $min and $max (both included).
     * @param $min float the minimum possible value (included).
     * @param $max float the maximum possible value (included).
     * @param $msg string the error message.
     */
    public function between($min, $max, $msg)
    {
        $this->min($min, $msg);
        $this->max($max, $msg);
        return $this;
    }
    
    /**
     * Checks that the value is at least the specified number of characters.
     * @param $len integer the minimum length (included).
     * @param $msg string the error message.
     */
    public function minLength($len, $msg)
    {
        if(strlen(trim($this->_value)) < $len) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value doesn't exceed $len number of characters.
     * @param $len integer the maximum number of characters (included).
     * @param $msg string the error message.
     */
    public function maxLength($len, $msg)
    {
        if(strlen(trim($this->_value)) > $len) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value's character length is between $min and $max.
     * @param $min integer the minimum possible number of characters (included).
     * @param $max integer the maximum possible number of characters (included).
     * @param $msg string the error message.
     */
    public function betweenLength($min, $max, $msg)
    {
        $this->minLength($min, $msg);
        $this->maxLength($max, $msg);
        return $this;
    }
    
    /**
     * Checks that the value's number of characters is exactly $len.
     * @param $len integer the required number of characters.
     * @param $msg string the error message.
     */
    public function length($len, $msg)
    {
        if(strlen(trim($this->_value)) != $len) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value equals the $tgt. This function also checks
     * the data type!
     * @param $msg string the error message.
     */
    public function equals($tgt, $msg)
    {
        if($this->_value !== $tgt) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is not equal to $tgt. This function
     * also checks the data type!
     * @param $msg string the error message.
     */
    public function differs($tgt, $msg)
    {
        if($this->_value === $tgt) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value starts with $str.
     * @param $str string the substring with which the value must start.
     * @param $msg string the error message.
     */
    public function startsWith($str, $msg)
    {
        if(strlen($this->_value) > 0 && substr($this->_value, 0, strlen($str)) !== $str) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value doesn't start with $str.
     * @param $str string the substring with which the value must not start.
     * @param $msg string the error message.
     */
    public function notStartsWith($str, $msg)
    {
        if(strlen($this->_value) > 0 && substr($this->_value, 0, strlen($str)) === $str) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value ends with $str.
     * @param $str string the substring with which the value must end.
     * @param $msg string the error message.
     */
    public function endsWith($str, $msg)
    {
        if(strlen($this->_value) > 0 && substr($this->_value, strlen($str) * -1) !== $str) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value doesn't end with $str.
     * @param $str string the substring with which the value must not end.
     * @param $msg string the error message.
     */
    public function notEndsWith($str, $msg)
    {
        if(strlen($this->_value) > 0 && substr($this->_value, strlen($str) * -1) === $str) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is an IP address. This checks both for IPv4
     * and IPv6.
     * @param $msg string the error message.
     */
    public function ip($msg)
    {
        if(strlen(trim($this->_value)) > 0 && filter_var($this->_value, FILTER_VALIDATE_IP) === FALSE) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is a URL.
     * @param $msg string the error message.
     */
    public function url($msg)
    {
        if(strlen(trim($this->_value)) > 0 && filter_var($this->_value, FILTER_VALIDATE_URL) === FALSE) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value is a date.
     * @param $msg string the error message.
     */
    public function date($msg)
    {
        if(strlen(trim($this->_value)) > 0) {
            try {
                $dt = new \DateTime($this->_value, new \DateTimeZone("UTC"));
            }
            catch(Exception $e) {
                $this->_errors[] = $msg;
            }
        }
        return $this;
    }
    
    /**
     * Checks that the value is at least the date $min.
     * @param $min mixed the minimum date.
     * @param $msg string the error message.
     */
    public function minDate($min, $msg)
    {
        if(is_numeric($min)) { // Probably an epoch.
            $min = new \DateTime('@' . $min);
        }
        else {
            $min = new \DateTime($min);
        }
        
        try {
            $dt = new \DateTime($this->_value, new \DateTimeZone("UTC"));
        }
        catch(Exception $e) {
            $this->_errors[] = $msg;
        }
        
        if($dt < $min) {
            $this->_errors[] = $msg;
        }
        
        return $this;
    }
    
    /**
     * Checks that the value is at most the date $max.
     * @param $max mixed the minimum date.
     * @param $msg string the error message.
     */
    public function maxDate($max, $msg)
    {
        if(is_numeric($max)) { // Probably an epoch.
            $max = new \DateTime('@' . $max);
        }
        else {
            $max = new \DateTime($max);
        }
        
        try {
            $dt = new \DateTime($this->_value, new \DateTimeZone("UTC"));
        }
        catch(Exception $e) {
            $this->_errors[] = $msg;
        }
        
        if($dt > $max) {
            $this->_errors[] = $msg;
        }
        
        return $this;
    }
    
    /**
     * Checks that the value is between the two dates $min and $max.
     * @param $min mixed the minimum date.     
     * @param $max mixed the minimum date.
     * @param $msg string the error message.
     */
    function betweenDates($min, $max, $msg)
    {
        $this->minDate($min, $msg);
        $this->maxDate($max, $msg);
    }
        
    /**
     * Checks that the value is one of multiple choices.
     * @param $choices array is an array of possible values.
     * @param $msg string the error message.
     */
    public function oneOf(array $choices, $msg)
    {
        if(!in_array($this->_value, $choices)) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value matches a regexp pattern.
     * @param $pattern string is the pattern to match.
     * @param $msg string the error message.
     */
    public function regexp($pattern, $msg)
    {
        if(!preg_match($pattern, (string)$this->_value)) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks that the value doesn't match a regexp pattern.
     * @param $pattern string is the pattern to not match against.
     * @param $msg string the error message.
     */
    public function notRegexp($pattern, $msg)
    {
        if(preg_match($pattern, (string)$this->_value)) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
    
    /**
     * Checks the value using a custom function.
     * example:
     *     $validator->callback(
     *         function($value) { return $value == 1; }),
     *         "The value isn't equal to 1"
     *     );
     * 
     * @param $func callable is a user-provided callback to check the value.
     *     if the callback returns FALSE, that will be considered an error.
     * @param $msg string the error message.
     */
    public function callback(callable $func, $msg)
    {
        if(!call_user_func($func, $this->_value)) {
            $this->_errors[] = $msg;
        }
        return $this;
    }
}
