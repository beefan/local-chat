<?php

namespace App\Services\SystemPromptService;

use App\Models\User;

interface SystemPromptServiceContract
{
  public function systemPromptsForUser(User $user): array;
}
