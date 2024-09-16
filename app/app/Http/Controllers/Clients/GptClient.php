<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GptClient implements ChatClientContract
{
  public function chat(array $messages): string
  {
    if (config('openai.testMode')) {
      Log::debug('GptClient#chat - test mode enabled', [
        'messages' => $messages,
      ]);
      return 'mock gpt response';
    }

    $response = Http::timeout(config('openai.responseTimeoutSeconds'))
      ->connectTimeout(config('openai.connectionTimeoutSeconds'))
      ->withHeaders([
        'Authorization' => 'Bearer ' . config('openai.apiKey'),
      ])
      ->post(
        config('openai.chatCompletionEndpoint'),
        [
          'model' => config('openai.model'),
          'messages' => $messages,
        ]
      );

    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody()->getContents(), true);

    Log::debug('GptClient#chat - response', [
      'body' => $body,
      'statusCode' => $statusCode,
    ]);

    return $body['choices'][0]['message']['content'];
  }
}
