@extends('layouts.publik')

@section('title', 'Struktur Organisasi — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@section('konten')
    <div class="bg-emerald-900 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Struktur Organisasi</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Yayasan sebagai badan hukum yang menaungi, dan Dewan Kemakmuran Masjid (DKM) yang menjalankan operasional ibadah serta kegiatan masjid.</p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 sm:px-6 lg:px-8">
        @forelse ($blok as $b)
            @php
                $e = $b['entitas'];
                $yayasan = $e->jenis === 'yayasan';
                $tema = $yayasan
                    ? ['bg-blue-900', 'text-blue-200', 'bg-blue-50 text-blue-700', 'border-blue-200', 'bg-blue-100 text-blue-700']
                    : ['bg-emerald-900', 'text-emerald-200', 'bg-emerald-50 text-emerald-700', 'border-emerald-200', 'bg-emerald-100 text-emerald-700'];
            @endphp
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 px-6 py-5 text-white sm:flex-row sm:items-center sm:justify-between {{ $tema[0] }}">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider {{ $tema[1] }}">{{ \App\Models\Entitas::JENIS[$e->jenis] ?? 'Unit' }}</div>
                        <h2 class="text-2xl font-bold">{{ $e->nama }}</h2>
                        @if ($e->induk)
                            <p class="mt-1 text-sm {{ $tema[1] }}">Di bawah naungan {{ $e->induk->nama }}</p>
                        @endif
                    </div>
                    @if ($b['struktur'])
                        <div class="rounded-lg bg-white/10 px-4 py-2 text-sm">
                            <div class="text-xs uppercase tracking-wider {{ $tema[1] }}">Periode</div>
                            @php $lp = $b['struktur']->label_periode; @endphp
                            <div class="font-semibold">{{ $b['struktur']->nama_struktur }}{{ $lp && ! str_contains($b['struktur']->nama_struktur, $lp) ? ' · ' . $lp : '' }}</div>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    @if ($b['kelompok']->isEmpty())
                        <p class="py-6 text-center text-sm text-slate-500">Data kepengurusan {{ $e->nama_pendek ?: $e->nama }} belum diisi.</p>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($b['kelompok'] as $jabatan => $orang)
                                <div class="rounded-xl border {{ $tema[3] }} bg-slate-50/60 p-4">
                                    <div class="mb-3 inline-block rounded px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $tema[4] }}">{{ $jabatan }}</div>
                                    <ul class="space-y-3">
                                        @foreach ($orang as $k)
                                            @php
                                                $p = $k->pengurus;
                                                $inisial = collect(explode(' ', trim($p->nama ?? '')))
                                                    ->filter(fn ($x) => preg_match('/^[A-Za-z]/', $x))
                                                    ->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))
                                                    ->take(2)->implode('');
                                            @endphp
                                            <li class="flex items-center gap-3">
                                                @if ($p?->foto)
                                                    <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama }}" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $tema[2] }}">{{ $inisial ?: 'P' }}</div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-semibold text-slate-800">{{ $p->nama ?? '-' }}</div>
                                                    @if ($p?->telepon)
                                                        <div class="text-xs text-slate-500">{{ $p->telepon }}</div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @empty
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center text-slate-500">
                Struktur organisasi belum tersedia.
            </div>
        @endforelse
    </div>
@endsection
