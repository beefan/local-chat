<?php

namespace App\Http\Controllers;

use App\Services\SystemPromptService\SystemPromptServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SystemPromptController extends Controller
{
    public function __construct(private SystemPromptServiceContract $systemPromptService) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $prompts = $this->systemPromptService->systemPromptsForUser($user);

        return Inertia::render('SystemPrompts', [
            'prompts' => $prompts,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:system_prompts',
            'prompt' => 'required|string',
        ]);

        $prompt = $this->systemPromptService->save(
            name: $request->input('name'),
            prompt: $request->input('prompt'),
            user: $request->user(),
        );

        return response()->json($prompt);
    }
}
