<?php

namespace Yupmin\Worker\Events;

use Exception;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Yupmin\Worker\Job\JobInterface;
use Yupmin\Worker\WorkerInterface;

class WorkerFailed
{
    use InteractsWithSockets, SerializesModels;

    /** @var WorkerInterface */
    public $worker;

    /** @var JobInterface */
    public $job;

    /** @var Exception */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkerInterface $worker, JobInterface $job, Exception $exception)
    {
        $this->worker = $worker;
        $this->exception = $exception;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
