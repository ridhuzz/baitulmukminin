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
        // Aset JS Livewire di-publish ke public/vendor/livewire (ikut repo) supaya
        // disajikan sebagai file statis — beberapa hosting (mis. DomaiNesia) tidak
        // meneruskan request berekstensi .js/.css ke PHP, sehingga route
        // /livewire/livewire.js bawaan akan 404. url() sudah sadar sub-folder.
        $basePath = SubfolderHandleRequests::basePath();

        if ($basePath !== '') {
            if (! file_exists(public_path('vendor/livewire/manifest.json'))) {
                config(['livewire.asset_url' => $basePath . '/livewire/livewire.js']);
            }

            $this->app->extend(HandleRequests::class, fn () => new SubfolderHandleRequests());
        }

        Event::listen(Login::class, function (Login $event): void {
            $event->user->forceFill(['last_login_at' => now()])->save();
        });
    }
}
