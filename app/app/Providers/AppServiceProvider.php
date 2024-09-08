<?php

namespace App\Providers;

use App\Http\Controllers\Clients\ChatClientContract;
use App\Http\Controllers\Clients\GptClient;
use App\Services\ChatService\ChatService;
use App\Services\ChatService\ChatServiceContract;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
