<?php

namespace App\Jobs;

use App\Models\Chat;
use App\Services\ChatService\ChatServiceContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SummarizeChat implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public Chat $chat) {}

    public function handle(ChatServiceContract $chatService): void
    {
        $chatService->summarizeChat($this->chat);
    }
}
