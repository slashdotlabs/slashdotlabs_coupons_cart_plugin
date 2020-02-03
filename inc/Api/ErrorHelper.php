<?php


namespace Slash\Api;


class ErrorHelper
{
    private $errors = [];

    public function addErrors(string $key, array $errors)
    {
        $this->errors[$key] = $errors;
        return $this;
    }

    public function errors($key = null)
    {
        if (empty($key)) return $this->errors;

        return $this->errors[$key] ?? [];
    }

    public function errorDisplay()
    {

    }
}