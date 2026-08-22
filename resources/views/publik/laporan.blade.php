@extends('layouts.publik')

@section('title', 'Transparansi Keuangan — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@php
    use App\Support\Tanggal;
    $rp = fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');
@endphp

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Transparansi Keuangan</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Kas Yayasan dan kas Masjid (DKM) dikelola dan dilaporkan secara terpisah. Pilih entitas untuk melihat posisi kas bulan berjalan dan laporan periodiknya.</p>

            @if ($daftarEntitas->count() > 1)
                <div class="mt-6 inline-flex rounded-lg bg-emerald-800/60 p-1">
                    @foreach ($daftarEntitas as $e)
                        <a href="{{ route('publik.laporan', $e->slug) }}"
                           class="rounded-md px-4 py-2 text-sm font-medium transition-all {{ $entitasAktif?->id === $e->id ? 'bg-white text-emerald-900 shadow-sm' : 'text-emerald-100 hover:text-white' }}">
                            {{ $e->nama_pendek ?: $e->nama }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if ($entitasAktif)
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $entitasAktif->jenis === 'yayasan' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $entitasAktif->nama }}
                </span>
                @if ($entitasAktif->keterangan)
                    <span class="text-sm text-slate-500">{{ $entitasAktif->keterangan }}</span>
                @endif
            </div>
        @endif

        <div class="mb-12 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">@svg('heroicon-o-wallet', 'h-6 w-6')</div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Total Saldo Kas</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['saldo']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">@svg('heroicon-o-arrow-trending-up', 'h-6 w-6')</div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Pemasukan {{ Tanggal::BULAN[now()->month] }} {{ now()->year }}</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['masuk']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600">@svg('heroicon-o-arrow-trending-down', 'h-6 w-6')</div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Pengeluaran {{ Tanggal::BULAN[now()->month] }} {{ now()->year }}</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['keluar']) }}</p>
            </div>
        </div>

        <h2 class="mb-6 text-2xl font-bold text-slate-800">Laporan Periodik {{ $entitasAktif?->nama_pendek ? '— ' . $entitasAktif->nama_pendek : '' }}</h2>
        @if ($laporan->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach ($laporan as $l)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-emerald-200 hover:shadow-md">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-lg font-bold text-slate-800">{{ $l->judul }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Periode {{ Tanggal::pendek($l->periode_mulai) }} – {{ Tanggal::pendek($l->periode_selesai) }}
                                </p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">Publik</span>
                        </div>
                        <dl class="mt-5 space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Saldo Awal</dt><dd class="font-medium text-slate-800">{{ $rp($l->saldo_awal) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Pemasukan</dt><dd class="font-medium text-emerald-600">+ {{ $rp($l->total_pemasukan) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Pengeluaran</dt><dd class="font-medium text-rose-600">- {{ $rp($l->total_pengeluaran) }}</dd></div>
                            <div class="flex justify-between border-t border-slate-100 pt-2 font-bold"><dt class="text-slate-800">Saldo Akhir</dt><dd class="text-slate-800">{{ $rp($l->saldo_akhir) }}</dd></div>
                        </dl>
                        @if ($l->catatan)
                            <p class="mt-4 rounded-lg bg-slate-50 p-3 text-xs leading-relaxed text-slate-600">{{ $l->catatan }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $laporan->links() }}</div>
        @else
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center text-slate-500">
                Belum ada laporan keuangan periodik {{ $entitasAktif?->nama_pendek }} yang dipublikasikan.
            </div>
        @endif
    </div>
@endsection
