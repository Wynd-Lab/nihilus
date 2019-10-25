<?php

namespace Nihilus;

use Error;

class Result
{
    /**
     * @var Error[]
     */
    private $errors = [];

    public function isSucceeded(): bool
    {
        return 0 === sizeof($this->errors);
    }

    public function addError(Error $error)
    {
        array_push($this->errors, $error);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
