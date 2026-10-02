@extends('layouts.publik')

@section('title', 'Donasi ZISWAF — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))
@section('deskripsi', 'Informasi lengkap Zakat, Infaq, Sedekah, dan Wakaf (ZISWAF) ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin') . ': QRIS resmi, rekening bank, panduan pembayaran, dan niat zakat.')

@php
    $qris = config('simasjid.donasi.qris', []);
    $rekening = config('simasjid.donasi.rekening', []);
@endphp

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Donasi ZISWAF</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Zakat, Infaq, Sedekah, dan Wakaf untuk kemakmuran {{ $masjid->nama_masjid ?? 'masjid' }} — dikelola amanah dan dilaporkan transparan.</p>
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl space-y-12 px-4 py-12 sm:px-6 lg:px-8">

        {{-- Apa itu ZISWAF --}}
        <section>
            <h2 class="mb-2 text-2xl font-bold text-slate-800">Apa itu ZISWAF?</h2>
            <p class="mb-6 max-w-3xl text-slate-500">Seluruh dana yang terkumpul dikelola pengurus secara amanah dan dilaporkan terbuka melalui halaman <a href="{{ route('publik.laporan') }}" class="font-medium text-emerald-700 hover:underline">Transparansi Keuangan</a>.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Zakat', 'heroicon-o-scale', 'Kewajiban harta bagi muslim yang telah mencapai nisab — zakat maal maupun zakat fitrah, disalurkan kepada yang berhak (asnaf).'],
                    ['Infaq', 'heroicon-o-hand-raised', 'Pemberian sukarela untuk operasional dan kemakmuran masjid: listrik, kebersihan, kajian, dan kegiatan jamaah.'],
                    ['Sedekah', 'heroicon-o-heart', 'Amal sukarela dalam bentuk apa pun — termasuk santunan yatim, dhuafa, dan kegiatan sosial masjid.'],
                    ['Wakaf', 'heroicon-o-building-library', 'Harta yang ditahan pokoknya agar manfaatnya mengalir terus: pembangunan, perluasan, dan sarana masjid.'],
                ] as [$judulZ, $ikonZ, $teksZ])
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">@svg($ikonZ, 'h-6 w-6')</span>
                        <h3 class="mb-2 font-bold text-slate-800">{{ $judulZ }}</h3>
                        <p class="text-sm leading-relaxed text-slate-600">{{ $teksZ }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- QRIS --}}
        <section>
            <h2 class="mb-2 text-2xl font-bold text-slate-800">Scan QRIS</h2>
            <p class="mb-6 text-slate-500">Gunakan aplikasi M-Banking atau e-wallet apa pun — satu QRIS untuk semua.</p>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:max-w-3xl">
                @foreach ($qris as $q)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                        <div class="mb-3">
                            <p class="font-bold text-slate-800">{{ $q['label'] }}</p>
                            <p class="text-xs tracking-wide text-slate-500">NMID : {{ $q['nmid'] }}</p>
                        </div>
                        <img src="{{ asset('images/' . $q['file']) }}" alt="{{ $q['label'] }} {{ $masjid->nama_masjid ?? 'Masjid' }}"
                             class="mx-auto aspect-square w-full max-w-[260px] rounded-xl border border-slate-100 bg-white p-2">
                        <p class="mt-3 text-sm font-semibold text-slate-700">{{ $masjid->nama_masjid ?? 'Masjid Baitul Mukminin' }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- Transfer bank --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-slate-800">
                    @svg('heroicon-o-building-library', 'h-6 w-6 text-emerald-600') Transfer Bank
                </h2>
                <div class="space-y-3">
                    @foreach ($rekening as $r)
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $r['bank'] }}</div>
                                <div class="font-mono text-base font-semibold tabular-nums text-slate-800">{{ $r['nomor'] }}</div>
                                <div class="text-xs text-slate-500">a.n. {{ $r['atas_nama'] }}</div>
                            </div>
                            <button type="button" data-salin="{{ $r['nomor'] }}"
                                    class="tombol-salin shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:border-emerald-300 hover:text-emerald-700">
                                Salin
                            </button>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2.5 text-xs leading-relaxed text-amber-800">
                    @svg('heroicon-o-shield-exclamation', 'mt-0.5 h-4 w-4 shrink-0')
                    <span>Sebelum transfer, pastikan nama penerima adalah <strong>Masjid Baitul Mukminin</strong>. Pengurus tidak pernah meminta transfer ke rekening atas nama pribadi.</span>
                </p>
            </section>

            {{-- Panduan + niat --}}
            <section class="space-y-6">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-6">
                    <h2 class="mb-3 flex items-center gap-2 text-xl font-bold text-emerald-800">
                        @svg('heroicon-o-clipboard-document-list', 'h-6 w-6') Panduan Pembayaran
                    </h2>
                    <ol class="list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-700">
                        <li>Scan QRIS dengan aplikasi M-Banking / e-wallet, atau transfer ke salah satu rekening masjid.</li>
                        <li>Simpan bukti transfer Anda.</li>
                        <li>Sampaikan konfirmasi kepada pengurus melalui halaman <a href="{{ route('publik.kontak') }}" class="font-medium text-emerald-700 underline">Hubungi Kami</a> agar donasi tercatat dalam laporan keuangan.</li>
                    </ol>
                </div>
                <div class="rounded-2xl bg-emerald-900 p-6 text-center text-white">
                    <h2 class="mb-3 flex items-center justify-center gap-2 text-sm font-semibold text-emerald-200">
                        @svg('heroicon-o-sparkles', 'h-4 w-4') Niat Menunaikan Zakat
                    </h2>
                    <p class="font-arab mb-3 text-2xl leading-relaxed" dir="rtl" lang="ar">نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ مَالِي فَرْضًا لِلّٰهِ تَعَالَى</p>
                    <p class="text-sm italic text-emerald-100">"Nawaitu an ukhrija zakata maali fardhan lillahi ta'ala."</p>
                    <p class="mt-2 text-xs leading-relaxed text-emerald-200">Aku niat mengeluarkan zakat hartaku fardhu karena Allah Ta'ala.</p>
                </div>
            </section>
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.tombol-salin').forEach(function (btn) {
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
        })();
    </script>
@endsection
