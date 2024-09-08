<?php

namespace App\Services\ChatService;

interface ChatServiceContract
{
  public function chat(array $messages, ?string $systemPrompt = null): string;
}
