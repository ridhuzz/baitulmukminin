<?php

namespace App\Support;

use Livewire\Mechanisms\HandleRequests\HandleRequests;

/**
 * Livewire menghasilkan URI endpoint "/livewire/update" relatif terhadap
 * root domain. Saat aplikasi dijalankan dari sub-folder (mis. MAMP:
 * http://localhost/baitul-mukminin/public), request dari browser jadi 404.
 * Kelas ini menambahkan path sub-folder dari APP_URL ke URI tersebut.
 * Di hosting dengan document root langsung ke /public, path-nya kosong
 * sehingga perilakunya sama dengan bawaan Livewire.
 */
class SubfolderHandleRequests extends HandleRequests
{
    public static function basePath(): string
    {
        return rtrim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');
    }

    public function getUpdateUri()
    {
        return static::basePath() . parent::getUpdateUri();
    }
}
