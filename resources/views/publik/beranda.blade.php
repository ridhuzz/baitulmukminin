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
        {{-- Titik kubah pada foto ≈ 58% lebar, 36% tinggi → dipetakan ke tengah hero agar masjid terlihat di tengah --}}
        <img src="{{ asset('images/masjid-baitul-mukminin.jpg') }}" alt="Masjid Baitul Mukminin"
             class="pointer-events-none absolute h-auto w-auto max-w-none select-none"
             style="left:50%;top:42%;min-width:140%;min-height:115%;transform:translate(-58%,-36%);">
        {{-- Overlay gradasi saja: pekat di kiri (area teks), makin transparan ke tengah/kanan agar foto masjid menonjol --}}
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/85 via-emerald-950/40 to-emerald-950/25"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/70 via-transparent to-emerald-950/30"></div>
        {{-- Motif bintang-oktagon emas, sangat tipis --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.05]" style="background-image:url('{{ asset('images/pattern-gold.svg') }}');background-size:128px 128px;"></div>
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
                    <a href="{{ route('publik.jadwal') }}" class="rounded-lg bg-white px-6 py-3 font-medium text-emerald-900 shadow-lg transition-colors hover:bg-emerald-50">
                        Lihat Jadwal
                    </a>
                    <button type="button" id="tombol-donasi" class="inline-flex items-center gap-2 rounded-lg border border-emerald-700 px-6 py-3 font-medium text-white transition-colors hover:bg-emerald-800">
                        @svg('heroicon-o-heart', 'h-5 w-5 text-emerald-300')
                        Donasi (ZISWAF)
                    </button>
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
                    <a href="{{ route('publik.jadwal') }}" class="mt-4 flex items-center justify-center rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100">
                        Jadwal lengkap &amp; sebulan penuh @svg('heroicon-o-arrow-right', 'ml-1 h-4 w-4')
                    </a>
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
        <div id="galeri" class="relative overflow-hidden bg-white py-16">
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
                                @if (!empty($foto['keterangan']))
                                    <span class="mt-0.5 block text-xs font-normal text-white/80">{{ $foto['keterangan'] }}</span>
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

    {{-- Modal Donasi ZISWAF (QRIS akan diganti dengan kode resmi; rekening menyusul dari pengurus) --}}
    <div id="modal-donasi" class="pointer-events-none fixed inset-0 z-[70] flex items-center justify-center p-4" aria-hidden="true">
        <div id="donasi-backdrop" class="absolute inset-0 bg-slate-900/60 opacity-0 transition-opacity duration-300"></div>
        <div id="donasi-panel" class="relative w-full max-w-3xl translate-y-4 scale-95 overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition-all duration-300"
             role="dialog" aria-modal="true" aria-labelledby="judul-donasi">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        @svg('heroicon-o-heart', 'h-5 w-5')
                    </span>
                    <div>
                        <h2 id="judul-donasi" class="text-lg font-bold text-slate-800">Donasi ZISWAF</h2>
                        <p class="text-xs text-slate-500">Zakat &middot; Infaq &middot; Sedekah &middot; Wakaf — scan QRIS di bawah ini</p>
                    </div>
                </div>
                <button type="button" id="tutup-donasi-x" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                    @svg('heroicon-o-x-mark', 'h-5 w-5')
                </button>
            </div>

            <div class="max-h-[70vh] space-y-6 overflow-y-auto p-6">
                {{-- Dua QRIS resmi, satu per bank --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        ['qris-btn.jpg', 'QRIS — BTN', 'NMID : ID1024325245081'],
                        ['qris-bjb.jpg', 'QRIS — BJB', 'NMID : ID1022233460016'],
                    ] as [$fileQr, $judulQr, $nmid])
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                            <div class="mb-3">
                                <p class="font-bold text-slate-800">{{ $judulQr }}</p>
                                <p class="text-xs tracking-wide text-slate-500">{{ $nmid }}</p>
                            </div>
                            <img src="{{ asset('images/' . $fileQr) }}" alt="{{ $judulQr }} {{ $masjid->nama_masjid ?? 'Masjid' }}"
                                 class="mx-auto aspect-square w-full max-w-[240px] rounded-xl bg-white p-2 shadow-sm">
                            <p class="mt-3 text-sm font-semibold text-slate-700">{{ $masjid->nama_masjid ?? 'Masjid Baitul Mukminin' }}</p>
                            <p class="text-xs text-slate-500">Satu QRIS untuk semua aplikasi pembayaran</p>
                        </div>
                    @endforeach
                </div>

                {{-- Rekening, panduan & niat --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h3 class="mb-3 flex items-center gap-2 font-semibold text-slate-800">
                            @svg('heroicon-o-building-library', 'h-5 w-5 text-emerald-600') Transfer Bank
                        </h3>
                        <div class="space-y-3">
                            @foreach ([['BTN', '0016101500667594'], ['BJB', '0027157981100']] as [$bank, $rek])
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $bank }}</div>
                                        <div class="font-mono text-sm font-semibold tabular-nums text-slate-800">{{ $rek }}</div>
                                        <div class="text-xs text-slate-500">a.n. Masjid Baitul Mukminin</div>
                                    </div>
                                    <button type="button" data-salin="{{ $rek }}"
                                            class="tombol-salin shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:border-emerald-300 hover:text-emerald-700">
                                        Salin
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-800">
                            @svg('heroicon-o-shield-exclamation', 'mt-0.5 h-4 w-4 shrink-0')
                            <span>Sebelum transfer, pastikan nama penerima adalah <strong>Masjid Baitul Mukminin</strong>. Pengurus tidak pernah meminta transfer ke rekening atas nama pribadi.</span>
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">
                            <h3 class="mb-2 flex items-center gap-2 font-semibold text-emerald-800">
                                @svg('heroicon-o-clipboard-document-list', 'h-5 w-5') Panduan Pembayaran
                            </h3>
                            <ol class="list-decimal space-y-1.5 pl-5 text-sm leading-relaxed text-slate-700">
                                <li>Scan QRIS dengan aplikasi M-Banking / e-wallet, atau transfer ke salah satu rekening masjid di atas.</li>
                                <li>Simpan bukti transfer dan lakukan konfirmasi ke pengurus{{ !empty($masjid?->kontak) ? ' melalui ' . $masjid->kontak : '' }}.</li>
                            </ol>
                        </div>
                        <div class="rounded-2xl bg-emerald-900 p-5 text-center text-white">
                            <h3 class="mb-3 flex items-center justify-center gap-2 text-sm font-semibold text-emerald-200">
                                @svg('heroicon-o-sparkles', 'h-4 w-4') Niat Menunaikan Zakat
                            </h3>
                            <p class="font-arab mb-3 text-xl leading-relaxed" dir="rtl" lang="ar">نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ مَالِي فَرْضًا لِلّٰهِ تَعَالَى</p>
                            <p class="text-sm italic text-emerald-100">"Nawaitu an ukhrija zakata maali fardhan lillahi ta'ala."</p>
                            <p class="mt-2 text-xs leading-relaxed text-emerald-200">Aku niat mengeluarkan zakat hartaku fardhu karena Allah Ta'ala.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 px-6 py-4 text-center">
                <button type="button" id="tutup-donasi" class="rounded-lg bg-emerald-600 px-10 py-2.5 font-semibold text-white transition-colors hover:bg-emerald-700">
                    TUTUP
                </button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var buka = document.getElementById('tombol-donasi');
            var modal = document.getElementById('modal-donasi');
            if (!buka || !modal) return;
            var backdrop = document.getElementById('donasi-backdrop');
            var panel = document.getElementById('donasi-panel');
            function setModal(terbuka) {
                modal.classList.toggle('pointer-events-none', !terbuka);
                modal.setAttribute('aria-hidden', terbuka ? 'false' : 'true');
                backdrop.classList.toggle('opacity-0', !terbuka);
                panel.classList.toggle('opacity-0', !terbuka);
                panel.classList.toggle('scale-95', !terbuka);
                panel.classList.toggle('translate-y-4', !terbuka);
                document.body.classList.toggle('overflow-hidden', terbuka);
            }
            buka.addEventListener('click', function () { setModal(true); });
            modal.querySelectorAll('.tombol-salin').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var teks = btn.getAttribute('data-salin');
                    (navigator.clipboard ? navigator.clipboard.writeText(teks) : Promise.reject()).then(function () {
                        btn.textContent = 'Tersalin ✓';
                        setTimeout(function () { btn.textContent = 'Salin'; }, 2000);
                    }).catch(function () {
                        window.prompt('Salin nomor rekening:', teks);
                    });
                });
            });
            backdrop.addEventListener('click', function () { setModal(false); });
            document.getElementById('tutup-donasi').addEventListener('click', function () { setModal(false); });
            document.getElementById('tutup-donasi-x').addEventListener('click', function () { setModal(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setModal(false); });
        })();
    </script>
@endsection
