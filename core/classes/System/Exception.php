<?php namespace System;


class Exception extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        Logger::log('Exception in file '.$this->getFile().': '.$message);
        parent::__construct($message, $code, $previous);
    }
} 