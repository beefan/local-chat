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

  public function save(string $name, string $prompt, ?int $id, User $user): SystemPrompt
  {
    if ($id) {
      $systemPrompt = $user->systemPrompts()->findOrFail($id);
      $systemPrompt->update([
        'name' => $name,
        'prompt' => $prompt,
      ]);

      return $systemPrompt;
    }

    return $user->systemPrompts()->create([
      'name' => $name,
      'prompt' => $prompt,
    ]);
  }

  public function default(): SystemPrompt
  {
    return SystemPrompt::where([
      'name' => 'default',
      'user_id' => null,
    ])->first();
  }

  private function mergeGlobalPrompts(array $prompts): array
  {
    return array_merge($prompts, SystemPrompt::whereNull('user_id')->get()->toArray());
  }
}
