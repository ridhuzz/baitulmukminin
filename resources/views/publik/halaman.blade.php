@extends('layouts.publik')

@section('title', $halaman->judul . ' — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))
@section('deskripsi', $halaman->ringkasan ?: 'Informasi ' . $halaman->judul . ' ' . ($masjid->nama_masjid ?? ''))

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">{{ $halaman->judul }}</h1>
            @if ($halaman->ringkasan)
                <p class="mt-2 max-w-2xl text-emerald-100">{{ $halaman->ringkasan }}</p>
            @endif
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            @if (filled($halaman->konten))
                <div class="prose-konten text-slate-700">{!! $halaman->konten !!}</div>
            @else
                <p class="text-center text-slate-500">Konten halaman ini belum diisi.</p>
            @endif
        </article>
        <p class="mt-4 text-right text-xs text-slate-400">Diperbarui {{ \App\Support\Tanggal::lengkap($halaman->updated_at) }}</p>
    </div>
@endsection
