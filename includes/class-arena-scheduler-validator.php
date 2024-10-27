<?php

/**
 * Validator Class
 *
 * This class provides validation functionality similar to Laravel's validation.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

class Arena_Scheduler_Validator
{
    private $errors = [];
    private $data = [];
    private $firstErrorOnly = false;

    /**
     * Validate the given data against the provided rules.
     *
     * @param array $data
     * @param array $rules
     * @param bool $firstErrorOnly
     * @return array
     */
    public function validate($data, $rules, $firstErrorOnly = false)
    {
        $this->data = $data;
        $this->firstErrorOnly = $firstErrorOnly;

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                $ruleName = $rule;
                $parameter = null;

                if (strpos($rule, ':') !== false) {
                    list($ruleName, $parameter) = explode(':', $rule);
                }

                if (method_exists($this, $ruleName)) {
                    $this->$ruleName($field, $parameter);
                }

                if ($this->firstErrorOnly && isset($this->errors[$field])) {
                    break 2; // Break both the inner and outer loop
                }
            }
        }

        return $this->errors;
    }

    /**
     * Validate that a field is required.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function required($field)
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            $this->errors[$field][] = "The $field field is required.";
        }
    }

    /**
     * Validate that a field is a string.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function string($field)
    {
        if (isset($this->data[$field]) && !is_string($this->data[$field])) {
            $this->errors[$field][] = "The $field must be a string.";
        }
    }

    /**
     * Validate that a field is a valid email address.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function email($field)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The $field must be a valid email address.";
        }
    }

    /**
     * Validate that a field has a minimum length.
     *
     * @param string $field The name of the field being validated.
     * @param int $parameter The minimum length.
     * @return void
     */
    private function min($field, $parameter)
    {
        if (strlen($this->data[$field]) < $parameter) {
            $this->errors[$field][] = "The $field must be at least $parameter characters long.";
        }
    }

    /**
     * Validate that a field has a maximum length.
     *
     * @param string $field The name of the field being validated.
     * @param int $parameter The maximum length.
     * @return void
     */
    private function max($field, $parameter)
    {
        if (strlen($this->data[$field]) > $parameter) {
            $this->errors[$field][] = "The $field must be no more than $parameter characters long.";
        }
    }

    /**
     * Validate that a field is a numeric value.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function numeric($field)
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = "The $field must be a numeric value.";
        }
    }

    /**
     * Validate that a field is a boolean value (true or false).
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function boolean($field)
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], [1, 0, '1', '0'], true)) {
            $this->errors[$field][] = "The $field field must be true or false.";
        }
    }

    /**
     * Validate that a field is a valid date.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function date($field)
    {
        if (isset($this->data[$field]) && !strtotime($this->data[$field])) {
            $this->errors[$field][] = "The $field must be a valid date.";
        }
    }

    /**
     * Validate that a field is a valid time.
     *
     * @param string $field The name of the field being validated.
     * @return void
     */
    private function time($field)
    {
        if (isset($this->data[$field]) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->data[$field])) {
            $this->errors[$field][] = "The $field must be a valid time.";
        }
    }

    /**
     * Validate that the end time is greater than the start time.
     *
     * @param string $field The name of the field being validated.
     * @param string $startTimeField The name of the start time field to compare against.
     * @return void
     */
    private function time_greater($field, $startTimeField)
    {
        $startTime = isset($this->data[$startTimeField]) ? intval($this->data[$startTimeField]) : null;
        $endTime = isset($this->data[$field]) ? intval($this->data[$field]) : null;

        if ($startTime !== null && $endTime !== null && $endTime <= $startTime) {
            $this->errors[$field][] = "The $field must be greater than the $startTimeField.";
        }
    }

    /**
     * Get the first validation error.
     *
     * @return string|null
     */
    public function firstError()
    {
        foreach ($this->errors as $error) {
            return $error[0];
        }
        return null;
    }

    /**
     * Send the validation response in JSON format using WordPress functions.
     *
     * @return void
     */
    public function sendValidationResponse()
    {
        if (!empty($this->errors)) {
            if ($this->firstErrorOnly) {
                wp_send_json_error([
                    'message' => $this->firstError(),
                    'errors' => [
                        'first' => $this->firstError()
                    ]
                ]);
            } else {
                wp_send_json_error([
                    'message' => $this->firstError(),
                    'errors' => $this->errors
                ]);
            }
        }
    }
}
