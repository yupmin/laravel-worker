<?php

declare(strict_types=1);

namespace Yupmin\Worker;

use Closure;

interface WorkerInterface
{
    public function setJobName(string $name): void;

    public function getJobName(): string;

    public function setConsoleCallback(Closure $consoleCallback): void;

    public function getConsoleCallback(): Closure;

    public function sendMessage(string $message): void;

    public function run(): void;
}
