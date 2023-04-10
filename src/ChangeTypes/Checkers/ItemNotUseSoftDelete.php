<?php


namespace Debuqer\EloquentMemory\ChangeTypes\Checkers;

class ItemNotUseSoftDelete extends ItemUseSoftDelete
{
    protected $notFlag = true;
}
