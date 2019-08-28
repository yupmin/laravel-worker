<?php

namespace Yupmin\Worker\Job;

use Closure;

interface JobInterface
{
    public function setMessage(string $message): void;

    public function execute(): void;
}
