@extends('layouts.publik')

@section('title', $item->judul . ' — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@php use App\Support\Tanggal; @endphp

@section('konten')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('publik.beranda') }}" class="mb-6 inline-flex items-center text-sm font-medium text-slate-500 transition-colors hover:text-emerald-600">
            @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
        </a>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:col-span-2">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="rounded bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">{{ $item->jenis }}</span>
                    <span class="text-xs text-slate-500">{{ Tanggal::lengkap($item->created_at) }}</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">{{ $item->judul }}</h1>
                @if ($item->tanggal_mulai)
                    <p class="mt-2 flex items-center text-sm text-slate-500">
                        @svg('heroicon-o-calendar-days', 'mr-2 h-4 w-4 text-emerald-600')
                        Berlaku {{ Tanggal::panjang($item->tanggal_mulai) }}@if ($item->tanggal_selesai) – {{ Tanggal::panjang($item->tanggal_selesai) }}@endif
                    </p>
                @endif
                @if ($item->gambar)
                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}" class="mt-6 w-full rounded-xl">
                @endif
                <div class="prose-konten mt-6 text-slate-700">{!! $item->konten !!}</div>
            </article>

            <aside class="space-y-6">
                @if ($lainnya->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center border-b border-slate-100 bg-slate-50/50 px-5 py-4 font-semibold text-slate-800">
                            @svg('heroicon-o-megaphone', 'mr-2 h-5 w-5 text-amber-500') Pengumuman Lainnya
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($lainnya as $p)
                                <a href="{{ route('publik.pengumuman', $p->slug) }}" class="group block p-4 transition-colors hover:bg-slate-50">
                                    <div class="mb-1 text-xs font-medium text-slate-500">{{ Tanggal::pendek($p->created_at) }}</div>
                                    <div class="text-sm font-medium text-slate-800 transition-colors group-hover:text-emerald-600">{{ $p->judul }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                <a href="{{ route('publik.laporan') }}" class="block rounded-2xl border border-emerald-800 bg-emerald-900 p-5 text-white shadow-lg transition-colors hover:bg-emerald-800">
                    <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-emerald-400">Transparansi</div>
                    <div class="font-semibold">Lihat laporan keuangan masjid</div>
                </a>
            </aside>
        </div>
    </div>
@endsection
