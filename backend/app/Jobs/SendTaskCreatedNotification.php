<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


/**
 * @ToDo in future change sync to queue (RabbitMQ, NATS или Kafka)
 */
class SendTaskCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $taskName
     * @param string|null $assigneeName
     * @param string $statusDisplayName
     */
    public function __construct(
        public string $taskName,
        public ?string $assigneeName,
        public string $statusDisplayName,
    ) {}


    public function handle(): void
    {
        Log::info("[notify] Task '{$this->taskName}' assigned to {$this->assigneeName} [{$this->statusDisplayName}]");
        /**
         * @ToDo add there sending notification to user into messenger, email or any other channel
         */
    }
}
