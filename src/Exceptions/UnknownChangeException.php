<?php


namespace Debuqer\EloquentMemory\Exceptions;


class UnknownChangeException extends \Exception
{
    protected $code = '1001';
    protected $message = 'List of changes in eloquent-memory.changes may have invalid change';
}
