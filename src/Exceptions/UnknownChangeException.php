<?php


namespace Debuqer\EloquentMemory\Exceptions;


class UnknownChangeException extends \Exception
{
    protected $code = '1001';
    protected $message = 'Unknown change';
}
