<?php

namespace App\Services\ChatService;

use App\Http\Controllers\Clients\ChatClientContract;
use App\Jobs\SummarizeChat;
use App\Models\Chat;
use App\Models\User;
use App\Services\SystemPromptService\SystemPromptServiceContract;

class ChatService implements ChatServiceContract
{
  public function __construct(
    private readonly ChatClientContract $chatClient,
    private readonly SystemPromptServiceContract $systemPromptService,
  ) {}

  public function chat(
    array $messages,
    ?int $userId = null,
    ?int $chatId = null,
    ?string $systemPrompt = null,
    ?int $systemPromptId = null
  ): ChatResponse {
    $chat = $chatId ? $this->getChat($chatId) : null;
    $saveMessages = $messages;

    if ($systemPromptId && ! $chat?->systemPrompt) {
      $systemPrompt = $this->systemPromptService->prompt($systemPromptId);
    } else {
      $systemPrompt = $systemPrompt ?? $chat?->systemPrompt?->prompt ?? $this->systemPromptService->default();
    }

    array_unshift($messages, [
      'role' => 'system',
      'content' => $systemPrompt,
    ]);

    if ($chat) {
      $summaryMessages = $chat->summary ? [
        [
          'role' => 'user',
          'content' => 'For context, here is a summary of our conversations up to this point: ' . $chat->summary,
        ]
      ] : [];
      $messages = array_merge($summaryMessages, $chat->lastMessages(), $messages);
    }

    $response = $this->chatClient->chat($messages);
    $saveMessages[] = [
      'role' => 'assistant',
      'content' => $response,
    ];

    if ($userId) {
      $chat = $this->saveChat($userId, $saveMessages, $chat, $systemPromptId);
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

  private function saveChat(?int $userId, array $messages, ?Chat $chat, ?int $systemPromptId): Chat
  {
    $messages = array_filter($messages, fn($message) => ! (empty($message['content']) || empty($message['role'])));

    if (! $chat) {
      $chat = Chat::create([
        'user_id' => $userId,
        'title' => $this->generateChatTitle($messages),
        ...$systemPromptId ? ['system_prompt_id' => $systemPromptId] : [],
      ]);
    }

    if ($chat->title === 'Untitled Chat') {
      $chat->update([
        'title' => $this->generateChatTitle($messages),
      ]);
    }

    $chat->messages()->createMany($messages);

    SummarizeChat::dispatch($chat);

    return $chat;
  }

  public function getChat(int $id): ?Chat
  {
    return Chat::find($id);
  }

  public function summarizeChat(Chat $chat): void
  {
    $currentMessageCount = $chat->messages()->count();
    $newMessageCount = $currentMessageCount - ($chat->last_summarized_message_count ?? 0);
    if ($newMessageCount < config('chat.summarizeChatThreshold')) {
      return;
    }

    $messages = $this->formatMessagesForSummary($chat->lastMessages($newMessageCount));

    if ($chat->summary) {
      $systemPrompt = config('chat.reSummarizeSystemPrompt') . "\nPrevious Summary: " . $chat->summary;
    } else {
      $systemPrompt = config('chat.summarizeSystemPrompt');
    }

    $query = [
      [
        'content' => $messages,
        'role' => 'user'
      ]
    ];
    $summary = $this->chat(messages: $query, systemPrompt: $systemPrompt)->systemResponse;
    $newTitle = $this->generateChatTitle(summary: $summary);

    $chat->summary = $summary;
    $chat->title = $newTitle;
    $chat->last_summarized_message_count = $currentMessageCount;
    $chat->save();
  }

  private function formatMessagesForSummary(array $messages): string
  {
    return implode("\n", array_map(fn($message) => $message['role'] . ': ' . $message['content'], $messages));
  }

  private function generateChatTitle(?array $messages = null, ?string $summary = null): string
  {
    // expects either messages or summary
    if ($messages) {
      $userChats = array_values(array_filter($messages, fn($message) => $message['role'] === 'user'));
      if (empty($userChats)) {
        return 'Untitled Chat';
      }
      $chatTitleQuery = 'Please generate a chat title for this message: ' . $userChats[0]['content'];
    }

    if ($summary) {
      $chatTitleQuery = 'Please generate a chat title for this summary: ' . $summary;
    }

    $query = [
      [
        'role' => 'user',
        'content' => $chatTitleQuery,
      ]
    ];

    $chat = $this->chat(messages: $query, systemPrompt: config('chat.chatTitleSystemPrompt'));

    return $chat->systemResponse;
  }
}
