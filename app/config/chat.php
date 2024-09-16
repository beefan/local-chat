<?php

return [
  'chatTitleSystemPrompt' => env('CHAT_TITLE_SYSTEM_PROMPT'),
  'lookbackMessages' => env('LOOKBACK_MESSAGES', 5),
  'summarizeChatThreshold' => env('SUMMARIZE_CHAT_THRESHOLD', 5),
  'summarizeSystemPrompt' => env('SUMMARIZE_SYSTEM_PROMPT'),
  'reSummarizeSystemPrompt' => env('RE_SUMMARIZE_SYSTEM_PROMPT'),
  'defaultSystemPrompt' => env('DEFAULT_SYSTEM_PROMPT', 'Chat with me!'),
];
