VALIDATOR MODULE FOR ASSEGAI
============================

This is a module to validate inputs in Assegai (and elsehwere).

A Quick Example
---------------

The example below shows how to throw validation exceptions with the custom
exception. You can then retrieve the error messages from the calling method.
It is not good practice to validate your data in your controller, this should
be handled in your Model. This is just a quick example.

    $validator = new \assegai\modules\validator\Validator($post);
    $validator
        ->required('You must supply a name.')
        ->validate('name', 'Name');
    $validator
        ->required('You must supply an email address.')
        ->email('You must supply a valid email address')
        ->validate('email', 'Email');

    // check for errors
    if ($validator->hasErrors()) {
        throw new Validator_Exception(
            'There were errors in your form.',
            $validator->getAllErrors()
            );
    }

Available Validation Methods
----------------------------

* exists($message = null) - The field must exist, regardless of it's content.
* required($message = null) - The field value is required.
* email($message = null) - The field value must be a valid email address string.
* float($message = null) - The field value must be a float.
* integer($message = null) - The field value must be an integer.
* digits($message = null) - The field value must be a digit (integer with no upper bounds).
* min($limit, $include = TRUE, $message = null) - The field value must be greater than $limit (numeric). $include defines if the value can be equal to the limit.
* max($limit, $include = TRUE, $message = null) - The field value must be less than $limit (numeric). $include defines if the value can be equal to the limit.
* between($min, $max, $include = TRUE, $message = null) - The field value must be between $min and $max (numeric). $include defines if the value can be equal to $min and $max.
* minLength($length, $message = null) - The field value must be greater than or equal to $length characters.
* maxLength($length, $message = null) - The field value must be less than or equal to $length characters.
* length($length, $message = null) - The field must be $length characters long.
* matches($field, $label, $message = null) - One field matches another one (i.e. password matching)
* notMatches($field, $label, $message = null) - The field value must not match the value of $field.
* startsWith($sub, $message = null) - The field must start with $sub as a string.
* notStartsWith($sub, $message = null) - The field must not start with $sub as a string.
* endsWith($sub, $message = null) - THe field must end with $sub as a string.
* notEndsWith($sub, $message = null) - The field must not end with $sub as a string.
* ip($message = null) - The field value is a valid IP, determined using filter_var.
* url($message = null) - The field value is a valid URL, determined using filter_var.
* date($message = null) - The field value is a valid date, can be of any format accepted by DateTime()
* minDate($date, $format, $message = null) - The date must be greater than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* maxDate($date, $format, $message = null) - The date must be less than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* ccnum($message = null) - The field value must be a valid credit card number.
* oneOf($allowed, $message = null) - The field value must be one of the $allowed values. $allowed can be either an array or a comma-separated list of values. If comma separated, do not include spaces unless intended for matching.
* callback($callback, $message = '', $params = null) - Define your own custom callback validation function. $callback must pass an is_callable() check. $params can be any value, or an array if multiple parameters must be passed.

Validating Arrays and Array Indices
-----------------------------------

This validation class has been extended to allow for validation of arrays as well as nested indices of a multi-dimensional array.

To validate specific indices of an array, use dot notation, i.e.

    // load the validator
    $validator = new \assegai\modules\validator\Validator($this->request->allPost());

    // ensure $_POST['field']['nested'] exists
    $validator
      ->required('The nested field is required.')
      ->validate('field.nested');

    // ensure we have the first two numeric
    // indices of $_POST['links'][]
    $validator
      ->required('This field is required')
      ->validate('links.0');
    $validator
      ->required('This field is required')
      ->validate('links.1');


Available Pre-Validation Filtering
----------------------------------

You can apply pre-validation filters to your data (i.e. trim, strip_tags, htmlentities). These filters can also
be custom defined so long as they pass an <code>is_callable()</code> check.

* filter($callback)

Examples:

    // standard php filter for valid user ids.
    $validator
      ->filter('intval')
      ->min(1)
      ->validate('user_id');

    // custom filter
    $validator
      ->filter(function($val) {
        // bogus formatting of the field
        $val = rtrim($val, '/');
        $val .= '_custom_formatted';
      })
      ->validate('field_to_be_formatted');
