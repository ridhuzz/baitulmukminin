<?php

namespace App\Providers;

use App\Support\SubfolderHandleRequests;
use Illuminate\Auth\Events\Login;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        // Aplikasi bisa berjalan di sub-folder (mis. MAMP: /baitul-mukminin/public).
        // Livewire secara bawaan memuat JS dari root domain (/livewire/livewire.js)
        // sehingga 404; arahkan ke path yang benar berdasarkan APP_URL.
        $basePath = SubfolderHandleRequests::basePath();
        config(['livewire.asset_url' => $basePath . '/livewire/livewire.js']);

        if ($basePath !== '') {
            $this->app->extend(HandleRequests::class, fn () => new SubfolderHandleRequests());
        }

        Event::listen(Login::class, function (Login $event): void {
            $event->user->forceFill(['last_login_at' => now()])->save();
        });
    }
}
