<?php

namespace App\Services\ChatService;

use App\Http\Controllers\Clients\ChatClientContract;
use App\Models\Chat;
use App\Models\User;

class ChatService implements ChatServiceContract
{
  public function __construct(
    private readonly ChatClientContract $chatClient
  ) {}

  public function chat(array $messages, ?int $userId = null, ?int $chatId = null, ?string $systemPrompt = null): ChatResponse
  {
    $chat = $chatId ? $this->getChat($chatId) : null;
    $saveMessages = $messages;

    if ($systemPrompt) {
      array_unshift($messages, [
        'role' => 'system',
        'content' => $systemPrompt,
      ]);
    }

    if ($chat) {
      $messages = array_merge($chat->lastMessages(), $messages);
    }

    $response = $this->chatClient->chat($messages);
    $saveMessages[] = [
      'role' => 'assistant',
      'content' => $response,
    ];

    if ($userId) {
      $chat = $this->saveChat($userId, $saveMessages, $chat);
    }

    return new ChatResponse(
      systemResponse: $response,
      chat: $chat,
    );
  }

  public function chatHistory(User $user): array
  {
    return $user->chats()->orderByDesc('created_at')->get()->toArray();
  }

  private function saveChat(?int $userId, array $messages, ?Chat $chat): Chat
  {
    $messages = array_filter($messages, fn($message) => ! (empty($message['content']) || empty($message['role'])));

    if (! $chat) {
      $chat = Chat::create([
        'user_id' => $userId,
        'title' => $this->generateChatTitle($messages),
      ]);
    }

    if ($chat->title === 'Untitled Chat') {
      $chat->update([
        'title' => $this->generateChatTitle($messages),
      ]);
    }

    $chat->messages()->createMany($messages);

    return $chat;
  }

  public function getChat(int $id): ?Chat
  {
    return Chat::find($id);
  }

  private function generateChatTitle(array $messages): string
  {
    $userChats = array_values(array_filter($messages, fn($message) => $message['role'] === 'user'));
    if (empty($userChats)) {
      return 'Untitled Chat';
    }

    $query = [
      [
        'role' => 'user',
        'content' => 'Please generate a chat title for this message: ' . $userChats[0]['content'],
      ]
    ];

    $chat = $this->chat(messages: $query, systemPrompt: config('chat.chatTitleSystemPrompt'));

    return $chat->systemResponse;
  }
}
