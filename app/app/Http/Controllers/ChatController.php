<?php

namespace App\Http\Controllers;

use App\Services\ChatService\ChatServiceContract;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
  public function __construct(
    private readonly ChatServiceContract $chatService
  ) {}

  public function chat(Request $request): Response
  {
    $messages = $request->get('messages');

    $response = $this->chatService->chat($messages);

    return Inertia::render('Chat', ['systemResponse' => $response]);
  }
}
