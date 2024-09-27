<?php

namespace App\Services\SystemPromptService;

use App\Models\SystemPrompt;
use App\Models\User;

class SystemPromptService implements SystemPromptServiceContract
{
  public function systemPromptsForUser(User $user): array
  {
    $userPrompts = $user->systemPrompts()->get()->toArray();

    return $this->mergeGlobalPrompts($userPrompts);
  }

  public function save(string $name, string $prompt, User $user): SystemPrompt
  {
    return $user->systemPrompts()->create([
      'name' => $name,
      'prompt' => $prompt,
    ]);
  }

  private function mergeGlobalPrompts(array $prompts): array
  {
    return array_merge($prompts, SystemPrompt::whereNull('user_id')->get()->toArray());
  }
}
