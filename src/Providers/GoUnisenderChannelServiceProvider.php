<?php

namespace NotificationChannels\GoUnisender\Providers;

use Illuminate\Support\ServiceProvider;
use NotificationChannels\GoUnisender\GoUnisenderApi;

class GoUnisenderChannelServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->singleton(GoUnisenderApi::class, static function ($app): GoUnisenderApi {
            $token = $app['config']->get('services.go_unisender.api_key');
            return new GoUnisenderApi($token);
        });
    }
}
