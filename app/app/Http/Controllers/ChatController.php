<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
  public function chat(Request $request): Response
  {
    logs()->info('request', ['messages' => $request->get('messages')]);
    return Inertia::render('Chat', ['chatMessage' => 'sounds good, what should I tell you about?']);
  }
}
