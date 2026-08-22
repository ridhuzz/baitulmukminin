<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Menu Ekstensi (Inventaris & Aset, Donatur, ZISWAF & Qurban)
    |--------------------------------------------------------------------------
    | Modul ini baru berupa halaman "segera hadir". Sembunyikan dari sidebar
    | dengan MENU_EKSTENSI=false di .env; set true untuk menampilkannya lagi.
    */
    'menu_ekstensi' => (bool) env('MENU_EKSTENSI', false),

];
