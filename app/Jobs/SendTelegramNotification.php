<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Services\TelegramService;

class SendTelegramNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int|string $chatId,
        protected string $text
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegram): void
    {
        $telegram->send($this->chatId, $this->text);
    }
}
