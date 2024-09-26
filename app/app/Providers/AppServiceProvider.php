<?php

namespace App\Providers;

use App\Http\Controllers\Clients\ChatClientContract;
use App\Http\Controllers\Clients\GptClient;
use App\Services\ChatService\ChatService;
use App\Services\ChatService\ChatServiceContract;
use App\Services\SystemPromptService\SystemPromptServiceContract;
use App\Services\SystemPromptService\SystemPromptService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChatClientContract::class, GptClient::class);
        $this->app->bind(ChatServiceContract::class, ChatService::class);
        $this->app->bind(SystemPromptServiceContract::class, SystemPromptService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
