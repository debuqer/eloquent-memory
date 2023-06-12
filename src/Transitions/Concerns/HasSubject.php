<?php


namespace Debuqer\EloquentMemory\Transitions\Concerns;


use Illuminate\Database\Eloquent\Model;

trait HasSubject
{
    protected $subject;

    public function getSubject()
    {
        return $this->subject;
    }

    public function getSubjectClass()
    {
        return get_class($this->getSubject());
    }

    public function setSubject(Model $subject)
    {
        $this->subject = $subject;
    }
}
