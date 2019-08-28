<?php

return [
    'driver' => env('WORKER_DRIVER', 'redis'),
    'drivers' => [
        'redis' => [],
        'amqp_basic' => [
            'host' => ENV('WORKER_AMQP_BASIC_HOST'),
            'port' => ENV('WORKER_AMQP_BASIC_PORT'),
            'user' => ENV('WORKER_AMQP_BASIC_USER'),
            'password' => ENV('WORKER_AMQP_BASIC_PASSWORD'),
            'vhost' => ENV('WORKER_AMQP_BASIC_VHOST', '/'),
            'durable' => ENV('WORKER_AMQP_BASIC_DURABLE', true),
        ],
    ],
    'jobs' => [
        'echo' => [
            'queue_name' => 'echo-queue',
            'job_class' => Yupmin\Worker\Job\EchoJob::class,
        ],
    ],
];
