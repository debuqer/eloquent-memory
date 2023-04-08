<?php


namespace Debuqer\EloquentMemory\Exceptions;


class NotRecognizedChangeException extends \Exception
{
    protected $code = '1002';
    protected $message = 'No change found based on the given old and new value';
}
