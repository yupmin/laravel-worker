<?php

declare(strict_types=1);

namespace Yupmin\Worker\Job;

class EchoJob implements JobInterface
{
    use JobTrait;

    public function execute(): void
    {
        //
    }
}
