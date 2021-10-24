<?php

namespace App\Exceptions;

class EmailNotVerifyException extends ExceptionBase
{
    protected $status = '403';

    public function __construct()
    {
        $msg = $this->build(func_get_args());

        parent::__construct($msg);
    }
}