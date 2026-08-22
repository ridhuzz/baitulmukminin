@extends('layouts.publik')

@section('title', 'Struktur Organisasi — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Struktur Organisasi</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Yayasan sebagai badan hukum yang menaungi, dan Dewan Kemakmuran Masjid (DKM) yang menjalankan operasional ibadah serta kegiatan masjid.</p>
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 sm:px-6 lg:px-8">
        @forelse ($blok as $b)
            <x-bagan-struktur :blok="$b" :ringkas="true" />
        @empty
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center text-slate-500">
                Struktur organisasi belum tersedia.
            </div>
        @endforelse
    </div>
@endsection
