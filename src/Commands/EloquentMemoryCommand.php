<?php

namespace Debuqer\EloquentMemory\Commands;

use Illuminate\Console\Command;

class EloquentMemoryCommand extends Command
{
    public $signature = 'eloquent-memory';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
