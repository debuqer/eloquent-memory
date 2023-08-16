<?php

namespace Debuqer\EloquentMemory\Transitions\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasSubject
{
    /** @var Model */
    protected $subject;

    /**
     * @return Model
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return false|string
     */
    public function getSubjectType()
    {
        return get_class($this->getSubject());
    }

    public function setSubject(Model $subject)
    {
        $this->subject = $subject;
    }
}
