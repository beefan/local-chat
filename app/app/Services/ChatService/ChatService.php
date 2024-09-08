<?php

namespace App\Services\ChatService;

use App\Http\Controllers\Clients\ChatClientContract;

class ChatService implements ChatServiceContract
{
  public function __construct(
    private readonly ChatClientContract $chatClient
  ) {}

  public function chat(array $messages, ?string $systemPrompt = null): string
  {
    if ($systemPrompt) {
      array_unshift($messages, [
        'role' => 'system',
        'content' => $systemPrompt,
      ]);
    }

    return $this->chatClient->chat($messages);
  }
}
