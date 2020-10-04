<?php


namespace App\Core\Validation;


abstract class AbstractValidator
{
    protected $errors;

    public function __construct()
    {
        $this->errors = [];
    }
}