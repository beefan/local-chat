<?php

namespace App\Services\ChatService;

use App\Models\Chat;

class ChatResponse
{
  public function __construct(
    public readonly string $systemResponse,
    public readonly ?Chat $chat,
  ) {}

  public function toArray(): array
  {
    return [
      'systemResponse' => $this->systemResponse,
      'chat' => $this->chat?->toArray() ?? null,
    ];
  }
}
