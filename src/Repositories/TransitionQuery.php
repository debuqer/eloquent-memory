<?php

namespace Debuqer\EloquentMemory\Repositories;

use Illuminate\Support\Str;

class TransitionQuery
{
    const DEFAULT_TAKE = 100;

    const DEFAULT_ORDER = 'desc';

    const DEFAULT_ORDER_KEY = 'date_recorded';

    const DEFAULT_CONDITIONS = [];

    protected $batch;

    protected $before;

    protected $after;

    protected $until;

    protected $from;

    protected $take = self::DEFAULT_TAKE;

    protected $order = self::DEFAULT_ORDER;

    protected $orderKey = self::DEFAULT_ORDER_KEY;

    protected $conditions = self::DEFAULT_CONDITIONS;


    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|null
     */
    public function __call(string $name, array $arguments)
    {
        if ( Str::startsWith($name, 'set') ) {
            return $this->setParameter(Str::substr($name, 3), $arguments[0]);
        } else if ( Str::startsWith($name, 'get') ) {
            return $this->getParameter(Str::substr($name, 3));
        } else {
            throw new \BadMethodCallException('Method '.$name.' does not exists in TransitionQuery');
        }
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function setParameter(string $name, $value)
    {
        if ( property_exists($this, Str::camel($name) ) ) {
            $this->{$name} = $value;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return null
     */
    public function getParameter(string $name)
    {
        if ( property_exists($this, $name) ) {
            return $this->{$name};
        }

        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isSeted(string $key)
    {
        return isset($this->{$key});
    }

}
