<?php

declare(strict_types=1);

namespace KayckMatias\Laravel\Socialite;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class DiscordServiceProvider extends ServiceProvider
{
    /**
     * Boot discord provider.
     */
    public function boot(): void
    {
        Socialite::extend('discord', function ($app) {
            return Socialite::buildProvider(
                provider: DiscordProvider::class,
                config: $app['config']['services.discord']
            );
        });
    }
}
