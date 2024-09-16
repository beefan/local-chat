<?php

namespace App\Services\ChatService;

use App\Models\Chat;
use App\Models\User;

interface ChatServiceContract
{
  public function chat(array $messages, ?int $userId, ?int $chatId = null, ?string $systemPrompt = null): ChatResponse;

  public function chatHistory(User $user): array;

  public function getChat(int $chatId): ?Chat;

  public function summarizeChat(Chat $chat): void;
}
