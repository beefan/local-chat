<?php

namespace App\Services\SystemPromptService;

use App\Models\SystemPrompt;
use App\Models\User;

interface SystemPromptServiceContract
{
  public function systemPromptsForUser(User $user): array;

  public function save(string $name, string $prompt, ?int $id, User $user): SystemPrompt;

  public function default(): SystemPrompt;
}
