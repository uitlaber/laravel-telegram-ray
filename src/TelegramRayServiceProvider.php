<?php
namespace Uitlaber\LaravelTelegramRay;

use Illuminate\Support\ServiceProvider;
use Uitlaber\LaravelTelegramRay\Services\TelegramRayService;

class TelegramRayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TelegramRayService::class, function ($app) {
            return new TelegramRayService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/telegram-ray.php', 'telegram-ray'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/telegram-ray.php' => config_path('telegram-ray.php'),
        ], 'config');
    }
}