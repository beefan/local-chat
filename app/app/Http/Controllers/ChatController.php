<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatService\ChatServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
  public function __construct(
    private readonly ChatServiceContract $chatService
  ) {}

  public function newChat(Request $request): Response
  {
    $user = $request->user();
    return Inertia::render('Chat', [
      'chatHistory' => $this->chatService->chatHistory($user),
      'prioritizeHistory' => false,
    ]);
  }

  public function chat(Request $request): JsonResponse
  {
    $userId = $request->user()->id;
    $chatId = $request->get('chatId');
    $messages = $request->get('messages');

    $response = $this->chatService->chat($messages, $userId, $chatId);

    return response()->json($response);
  }

  public function chatHistory(Request $request): Response
  {
    $user = $request->user();
    return Inertia::render('Chat', [
      'chatHistory' => $this->chatService->chatHistory($user),
      'prioritizeHistory' => true,
    ]);
  }

  public function getChat(Chat $chat): Response
  {
    return Inertia::render('Chat', [
      'title' => $chat->title,
      'id' => $chat->id,
      'messageHistory' => $chat->messages()->get()->toArray(),
      'chatHistory' => fn() => $this->chatService->chatHistory($chat->user),
      'prioritizeHistory' => false,
    ]);
  }
}
