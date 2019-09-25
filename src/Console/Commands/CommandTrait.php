<?php

declare(strict_types=1);

namespace Yupmin\Worker\Console\Commands;

use Illuminate\Support\Carbon;

trait CommandTrait
{
    public function withTimestampNow(): string
    {
        return '['.Carbon::now('UTC')->format('c').'] '.$this->getName().' ';
    }
}
