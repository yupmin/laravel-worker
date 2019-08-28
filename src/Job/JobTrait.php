<?php

namespace Yupmin\Worker\Job;

trait JobTrait
{
    /** @var string */
    protected $message;

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
