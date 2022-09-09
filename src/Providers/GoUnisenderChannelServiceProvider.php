<?php

namespace NotificationChannels\GoUnisender\Providers;

use Illuminate\Support\ServiceProvider;
use NotificationChannels\GoUnisender\GoUnisenderApi;

class GoUnisenderChannelServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(GoUnisenderApi::class, function ($app) {
            $token = $app['config']->get('services.unisender.api-key');

            return new GoUnisenderApi($token);
        });
    }
}
