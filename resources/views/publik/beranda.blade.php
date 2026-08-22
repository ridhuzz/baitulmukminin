@extends('layouts.publik')

@php
    use App\Support\Tanggal;
    $rp = fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');
    $warnaKategori = ['emerald', 'blue', 'amber', 'violet'];
@endphp

@section('konten')
    {{-- Hero --}}
    <div class="relative overflow-hidden bg-emerald-950 text-white">
        {{-- Foto Masjid Baitul Mukminin sebagai latar --}}
        <img src="{{ asset('images/masjid-baitul-mukminin.jpg') }}" alt="Masjid Baitul Mukminin"
             class="absolute inset-0 h-full w-full object-cover object-center">
        {{-- Overlay gelap agar teks terbaca (lebih pekat di kiri) --}}
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/80 to-emerald-900/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-transparent to-emerald-950/40"></div>
        {{-- Overlay pola geometri islami nuansa emas --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.16]" style="background-image:url('{{ asset('images/pattern-gold.svg') }}');background-size:96px 96px;"></div>
        {{-- Bingkai ornamen emas atas & bawah --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-11 rotate-180 opacity-70" style="background-image:url('{{ asset('images/ornament-band.svg') }}');background-repeat:repeat-x;background-position:center bottom;"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-11 opacity-80" style="background-image:url('{{ asset('images/ornament-band.svg') }}');background-repeat:repeat-x;background-position:center bottom;"></div>
        <div class="relative z-10 mx-auto flex max-w-7xl flex-col items-center gap-12 px-4 py-20 pb-24 sm:px-6 md:flex-row lg:px-8 lg:py-28 lg:pb-32">
            <div class="flex-1 space-y-6 text-center md:text-left">
                <span class="rounded-full border border-emerald-700/50 bg-emerald-800/50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-300">
                    Pusat Ibadah &amp; Kegiatan Umat
                </span>
                <h1 class="text-4xl font-bold leading-tight md:text-5xl lg:text-6xl">
                    {{ $masjid->nama_masjid ?? 'Masjid Baitul Mukminin' }}
                </h1>
                <p class="mx-auto max-w-2xl text-lg leading-relaxed text-emerald-100 md:mx-0">
                    {{ $masjid->deskripsi ?: 'Selamat datang di portal informasi jamaah. Dapatkan update jadwal shalat, kegiatan kajian, dan transparansi laporan keuangan masjid secara real-time.' }}
                </p>
                @if (!empty($masjid?->alamat))
                    <p class="flex items-center justify-center gap-2 text-sm text-emerald-200 md:justify-start">
                        @svg('heroicon-o-map-pin', 'h-4 w-4 shrink-0')
                        <span>{{ $masjid->alamat }}@if(!empty($masjid->kota)), {{ $masjid->kota }}@endif</span>
                    </p>
                @endif
                <div class="flex items-center justify-center gap-4 pt-4 md:justify-start">
                    <a href="#jadwal" class="rounded-lg bg-white px-6 py-3 font-medium text-emerald-900 shadow-lg transition-colors hover:bg-emerald-50">
                        Lihat Jadwal
                    </a>
                    <a href="#keuangan" class="rounded-lg border border-emerald-700 px-6 py-3 font-medium text-white transition-colors hover:bg-emerald-800">
                        Transparansi
                    </a>
                </div>
            </div>

            {{-- Kartu waktu shalat --}}
            <div id="jadwal" class="w-full max-w-md flex-1 scroll-mt-24">
                <div class="transform rounded-2xl bg-white p-6 text-slate-800 shadow-2xl transition-transform md:rotate-1 hover:rotate-0">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="flex items-center text-lg font-bold">
                            @svg('heroicon-o-clock', 'mr-2 h-5 w-5 text-emerald-600') Waktu Shalat
                        </h3>
                        <span class="rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-500">{{ Tanggal::panjang(today()) }}</span>
                    </div>
                    <div class="space-y-3">
                        @foreach ($waktuShalat as $nama => $jam)
                            @if ($nama === $shalatAktif)
                                <div class="relative flex items-center justify-between overflow-hidden rounded-lg border border-emerald-100 bg-emerald-50 p-3 shadow-inner">
                                    <div class="absolute bottom-0 left-0 top-0 w-1 bg-emerald-500"></div>
                                    <span class="font-bold text-emerald-700">{{ $nama }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="hidden animate-pulse text-xs font-medium text-emerald-600 sm:inline">Sedang Berlangsung</span>
                                        <span class="text-lg font-bold text-emerald-800">{{ $jam ?? '--:--' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between rounded-lg p-3 hover:bg-slate-50">
                                    <span class="font-medium text-slate-600">{{ $nama }}</span>
                                    <div class="flex items-center gap-3">
                                        @if ($nama === $shalatBerikutnya)
                                            <span class="hidden text-xs font-medium text-slate-400 sm:inline">Berikutnya</span>
                                        @endif
                                        <span class="font-bold text-slate-800">{{ $jam ?? '--:--' }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if ($jadwalJumat)
                        <div class="mt-5 border-t border-slate-100 pt-4 text-sm">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-emerald-600">Shalat Jumat Berikutnya</div>
                            <div class="font-semibold text-slate-800">{{ Tanggal::lengkap($jadwalJumat->tanggal_jadwal) }}</div>
                            <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-slate-600">
                                @foreach ($jadwalJumat->detail as $d)
                                    <span><span class="text-slate-400">{{ ucfirst($d->peran) }}:</span> {{ $d->petugas->nama ?? '-' }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Transparansi keuangan --}}
    <div id="keuangan" class="mx-auto max-w-7xl scroll-mt-20 px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h2 class="mb-2 text-2xl font-bold text-slate-800">Transparansi Keuangan</h2>
            <p class="text-slate-500">
                Kas {{ $entitasKeuangan?->nama ?? 'masjid' }} bulan berjalan ({{ Tanggal::BULAN[now()->month] }} {{ now()->year }})
                &middot; <a href="{{ route('publik.laporan') }}" class="font-medium text-emerald-600 hover:underline">lihat kas Yayasan &amp; laporan periodik</a>
            </p>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all hover:border-blue-200 hover:shadow-md">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    @svg('heroicon-o-wallet', 'h-6 w-6')
                </div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Total Saldo Kas</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['saldo']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all hover:border-emerald-200 hover:shadow-md">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    @svg('heroicon-o-arrow-trending-up', 'h-6 w-6')
                </div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Pemasukan Bulan Ini</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['masuk']) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all hover:border-rose-200 hover:shadow-md">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    @svg('heroicon-o-arrow-trending-down', 'h-6 w-6')
                </div>
                <h3 class="mb-1 text-sm font-medium text-slate-500">Pengeluaran Bulan Ini</h3>
                <p class="text-3xl font-bold text-slate-800">{{ $rp($keuangan['keluar']) }}</p>
            </div>
        </div>
        @if ($laporan->isNotEmpty())
            <div class="mt-8 text-center">
                <a href="{{ route('publik.laporan') }}" class="inline-flex items-center rounded-lg bg-emerald-50 px-4 py-2 font-medium text-emerald-600 transition-colors hover:text-emerald-700">
                    Lihat Laporan Keuangan Periodik @svg('heroicon-o-arrow-right', 'ml-1 h-4 w-4')
                </a>
            </div>
        @endif
    </div>

    {{-- Kegiatan mendatang --}}
    <div id="kegiatan" class="scroll-mt-20 bg-slate-100 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h2 class="mb-2 text-2xl font-bold text-slate-800">Kegiatan Mendatang</h2>
                    <p class="text-slate-500">Ikuti berbagai program, kajian, dan aktivitas jamaah</p>
                </div>
                <a href="{{ route('publik.kegiatan') }}" class="flex items-center rounded-lg bg-emerald-50 px-4 py-2 font-medium text-emerald-600 transition-colors hover:text-emerald-700">
                    Semua Kegiatan @svg('heroicon-o-arrow-right', 'ml-1 h-4 w-4')
                </a>
            </div>

            @if ($kegiatan->isNotEmpty())
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach ($kegiatan as $k)
                        @php $w = $warnaKategori[$loop->index % count($warnaKategori)]; @endphp
                        @include('publik._kartu-kegiatan', ['k' => $k, 'w' => $w])
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white/60 py-12 text-center text-slate-500">
                    Belum ada kegiatan terjadwal. Pantau terus halaman ini untuk informasi terbaru.
                </div>
            @endif
        </div>
    </div>

    {{-- Galeri / suasana masjid --}}
    @if (count($galeri))
        <div class="relative overflow-hidden bg-white py-16">
            <div class="pointer-events-none absolute inset-0 opacity-[0.04]" style="background-image:url('{{ asset('images/pattern-islamic.svg') }}');background-size:96px 96px;"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <h2 class="mb-2 text-2xl font-bold text-slate-800">Suasana &amp; Fasilitas Masjid</h2>
                        <p class="text-slate-500">Dokumentasi kegiatan, fasilitas, dan keseharian jamaah {{ $masjid->nama_masjid ?? 'masjid' }}</p>
                    </div>
                    <div class="font-arab hidden text-2xl text-emerald-700/70 sm:block" dir="rtl" lang="ar">{{ \App\Support\Brand::NAMA_ARAB }}</div>
                </div>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach ($galeri as $i => $foto)
                        <figure class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm {{ $i === 0 ? 'col-span-2 row-span-2' : '' }}">
                            <img src="{{ $foto['url'] }}" alt="{{ $foto['judul'] }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 {{ $i === 0 ? 'min-h-[260px] md:min-h-full' : 'aspect-[4/3]' }}">
                            <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/80 to-transparent px-4 pb-3 pt-10 text-sm font-medium text-white">
                                {{ $foto['judul'] }}
                                @if ($foto['dummy'])
                                    <span class="ml-1 rounded bg-white/20 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider">ilustrasi</span>
                                @endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Pengumuman --}}
    @if ($pengumuman->isNotEmpty())
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="mb-2 text-2xl font-bold text-slate-800">Pengumuman &amp; Informasi</h2>
                <p class="text-slate-500">Informasi terbaru dari pengurus untuk jamaah</p>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($pengumuman as $p)
                    <a href="{{ route('publik.pengumuman', $p->slug) }}"
                       class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-emerald-300 hover:shadow-md">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="rounded bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">{{ $p->jenis }}</span>
                            <span class="text-xs text-slate-500">{{ Tanggal::pendek($p->created_at) }}</span>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-slate-800 transition-colors group-hover:text-emerald-600">{{ $p->judul }}</h3>
                        <p class="line-clamp-3 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $p->konten)), 140) }}</p>
                        <span class="mt-4 inline-flex items-center text-sm font-medium text-emerald-600">Baca selengkapnya @svg('heroicon-o-arrow-right', 'ml-1 h-4 w-4')</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
