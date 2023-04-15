<?php

namespace Debuqer\EloquentMemory;

use Debuqer\EloquentMemory\ChangeTypes\ChangeTypeInterface;
use Debuqer\EloquentMemory\Exceptions\NotRecognizedChangeException;

class EloquentMemory
{
    public function detect($old, $new)
    {
        /** @var ChangeTypeInterface $changeType */
        foreach (config('eloquent-memory.changes', []) as $changeType) {
            if ( $changeType::isApplicable($old, $new) ) {
                return $changeType::create($old, $new);
            }
        }

        throw new NotRecognizedChangeException();
    }
}
