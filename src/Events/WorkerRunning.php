<?php

declare(strict_types=1);

namespace Yupmin\Worker\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Yupmin\Worker\Job\JobInterface;
use Yupmin\Worker\WorkerInterface;

class WorkerRunning
{
    use InteractsWithSockets, SerializesModels;

    /** @var WorkerInterface */
    public $worker;

    /** @var JobInterface */
    public $job;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WorkerInterface $worker, JobInterface $job)
    {
        $this->worker = $worker;
        $this->job = $job;
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
