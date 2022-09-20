<?php

namespace NotificationChannels\GoUnisender\Providers;

use Illuminate\Support\ServiceProvider;
use NotificationChannels\GoUnisender\GoUnisenderApi;

class GoUnisenderChannelServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(GoUnisenderApi::class, function ($app) {
            $token = env('GO_UNISENDER_API_KEY', NULL);
            return new GoUnisenderApi($token);
        });
    }
}
