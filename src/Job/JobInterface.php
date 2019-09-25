<?php

declare(strict_types=1);

namespace Yupmin\Worker\Job;

interface JobInterface
{
    public function setMessage(string $message): void;

    public function execute(): void;
}
