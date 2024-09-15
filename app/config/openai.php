<?php

return [
  'connectionTimeoutSeconds' => 60 * 10,
  'responseTimeoutSeconds' => 60 * 10,
  'chatCompletionEndpoint' => 'https://api.openai.com/v1/chat/completions',
  'model' => 'gpt-4o',
  'apiKey' => env('OPENAI_API_KEY'),
  'testMode' => env('OPENAI_TEST_MODE', false),
];
