<?php
namespace App\Services;

class Validator
{
    public $errors = [];

    public function required($field, $value, $message = null)
    {

        if (empty(trim($value))) {
            $this->errors[$field] = $message ?? "$field is required.";
        }
    }

    public function email($field, $value, $message = null)
    {

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Invalid email format.";
        }
    }

    public function minLength($field, $value, $min, $message = null)
    {

        if (strlen($value) < $min) {
            $this->errors[$field] = $message ?? "$field must be at least $min characters.";
        }
    }

    public function maxLength($field, $value, $max, $message = null)
    {

        if (strlen($value) > $max) {
            $this->errors[$field] = $message ?? "$field cannot exceed $max characters.";
        }
    }

    public function passes()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
