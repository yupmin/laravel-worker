<?php

namespace Yupmin\Worker\Console\Commands;

use Illuminate\Support\Carbon;

trait CommandTrait
{
    public function withTimestampNow()
    {
        return '['.Carbon::now('UTC')->format('c').'] '.$this->getName().' ';
    }
}
