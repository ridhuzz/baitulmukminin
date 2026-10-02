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

    /*
    |--------------------------------------------------------------------------
    | Donasi ZISWAF
    |--------------------------------------------------------------------------
    | Satu sumber data untuk modal donasi di beranda dan halaman /donasi.
    | Gambar QRIS ada di public/images/.
    */
    'donasi' => [
        'qris' => [
            ['file' => 'qris-btn.jpg', 'label' => 'QRIS — BTN', 'nmid' => 'ID1024325245081'],
            ['file' => 'qris-bjb.jpg', 'label' => 'QRIS — BJB', 'nmid' => 'ID1022233460016'],
        ],
        'rekening' => [
            ['bank' => 'BTN', 'nomor' => '0016101500667594', 'atas_nama' => 'Masjid Baitul Mukminin'],
            ['bank' => 'BJB', 'nomor' => '0027157981100', 'atas_nama' => 'Masjid Baitul Mukminin'],
        ],
    ],

];
