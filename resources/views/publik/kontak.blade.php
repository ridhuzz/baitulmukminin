@extends('layouts.publik')

@section('title', 'Hubungi Kami — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))
@section('deskripsi', 'Alamat, kontak, dan formulir pesan untuk ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@php
    $alamatLengkap = trim(($masjid?->alamat ?? '') . (filled($masjid?->kota) ? ', ' . $masjid->kota : '') . (filled($masjid?->provinsi) ? ', ' . $masjid->provinsi : ''), ', ');
    // Nomor WhatsApp dari Profil Masjid → format wa.me (08xx → 628xx)
    $waDigit = preg_replace('/\D+/', '', (string) ($masjid->kontak ?? ''));
    $waDigit = $waDigit !== '' ? preg_replace('/^0/', '62', $waDigit) : null;
@endphp

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Hubungi Kami</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Sampaikan pertanyaan, saran, atau kebutuhan Anda — pengurus {{ $masjid->nama_masjid ?? 'masjid' }} siap membantu.</p>
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-5">

            {{-- Form Kirim Pesan (diteruskan ke WhatsApp sekretariat) --}}
            <section class="lg:col-span-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="mb-1 flex items-center gap-2 text-xl font-bold text-slate-800">
                        @svg('heroicon-o-chat-bubble-left-right', 'h-6 w-6 text-emerald-600') Kirim Pesan
                    </h2>
                    <p class="mb-6 text-sm text-slate-500">Pesan akan diteruskan melalui WhatsApp ke sekretariat masjid.</p>
                    <form id="form-kontak" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="k-nama" class="mb-1 block text-sm font-medium text-slate-700">Nama</label>
                                <input id="k-nama" type="text" required maxlength="80" placeholder="Nama lengkap"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label for="k-telepon" class="mb-1 block text-sm font-medium text-slate-700">No. Telepon <span class="font-normal text-slate-400">(opsional)</span></label>
                                <input id="k-telepon" type="tel" maxlength="20" placeholder="08xx-xxxx-xxxx"
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div>
                            <label for="k-subjek" class="mb-1 block text-sm font-medium text-slate-700">Subjek</label>
                            <select id="k-subjek" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option>Pertanyaan Umum</option>
                                <option>Konfirmasi Donasi / ZISWAF</option>
                                <option>Peminjaman Fasilitas / Aula</option>
                                <option>Kegiatan &amp; Kajian</option>
                                <option>Saran &amp; Masukan</option>
                                <option>Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="k-pesan" class="mb-1 block text-sm font-medium text-slate-700">Pesan</label>
                            <textarea id="k-pesan" rows="5" required maxlength="1000" placeholder="Tulis pesan Anda…"
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 font-medium text-white transition-colors hover:bg-emerald-700 {{ $waDigit ? '' : 'cursor-not-allowed opacity-60' }}"
                                @unless ($waDigit) disabled title="Nomor WhatsApp sekretariat belum diisi di Profil Masjid" @endunless>
                            @svg('heroicon-o-paper-airplane', 'h-5 w-5') Kirim via WhatsApp
                        </button>
                        @unless ($waDigit)
                            <p class="text-xs text-amber-700">Nomor WhatsApp sekretariat belum diatur — isi kolom Kontak pada Admin → Profil Masjid &amp; Yayasan.</p>
                        @endunless
                    </form>
                </div>

                {{-- Peta lokasi --}}
                @if ($alamatLengkap)
                    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center gap-2 border-b border-slate-100 px-6 py-4 font-semibold text-slate-800">
                            @svg('heroicon-o-map', 'h-5 w-5 text-emerald-600') Lokasi Masjid
                        </div>
                        <iframe title="Peta lokasi {{ $masjid->nama_masjid ?? 'masjid' }}"
                                src="https://www.google.com/maps?q={{ urlencode(($masjid->nama_masjid ?? 'Masjid') . ' ' . $alamatLengkap) }}&output=embed"
                                class="h-80 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                @endif
            </section>

            {{-- Info kontak --}}
            <aside class="space-y-5 lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-slate-800">Informasi Kontak</h2>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">@svg('heroicon-o-map-pin', 'h-5 w-5')</span>
                            <span>
                                <span class="block font-semibold text-slate-800">Alamat</span>
                                <span class="leading-relaxed text-slate-600">{{ $alamatLengkap ?: 'Alamat belum diisi.' }}</span>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">@svg('heroicon-o-phone', 'h-5 w-5')</span>
                            <span>
                                <span class="block font-semibold text-slate-800">Telepon / WhatsApp</span>
                                @if (filled($masjid?->kontak))
                                    <a href="{{ $waDigit ? 'https://wa.me/' . $waDigit : '#' }}" target="_blank" rel="noopener" class="text-emerald-700 hover:underline">{{ $masjid->kontak }}</a>
                                @else
                                    <span class="text-slate-600">Belum diisi.</span>
                                @endif
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">@svg('heroicon-o-envelope', 'h-5 w-5')</span>
                            <span>
                                <span class="block font-semibold text-slate-800">Email</span>
                                @if (filled($masjid?->email))
                                    <a href="mailto:{{ $masjid->email }}" class="text-emerald-700 hover:underline">{{ $masjid->email }}</a>
                                @else
                                    <span class="text-slate-600">Belum diisi.</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-slate-800">
                        @svg('heroicon-o-clock', 'h-5 w-5 text-emerald-600') Jam Sekretariat
                    </h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-600">Senin – Sabtu</dt><dd class="font-medium text-slate-800">08.00 – 15.00 WIB</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-600">Ahad / Hari Libur</dt><dd class="font-medium text-slate-800">Dengan perjanjian</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-600">Shalat Jumat</dt><dd class="font-medium text-slate-800">11.30 – 12.30 WIB</dd></div>
                    </dl>
                    <p class="mt-3 text-xs text-slate-400">Masjid terbuka untuk jamaah pada seluruh waktu shalat.</p>
                </div>

                <div class="rounded-2xl bg-emerald-900 p-6 text-white shadow-lg">
                    <h2 class="mb-2 text-lg font-bold">Konfirmasi Donasi</h2>
                    <p class="mb-4 text-sm leading-relaxed text-emerald-100">Sudah berdonasi? Sampaikan konfirmasi transfer Anda agar tercatat dalam laporan keuangan masjid.</p>
                    <a href="{{ route('publik.beranda') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-emerald-900 transition-colors hover:bg-emerald-50">
                        @svg('heroicon-o-heart', 'h-4 w-4') Lihat Info Donasi
                    </a>
                </div>
            </aside>
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('form-kontak');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                @if ($waDigit)
                var nama = document.getElementById('k-nama').value.trim();
                var telp = document.getElementById('k-telepon').value.trim();
                var subjek = document.getElementById('k-subjek').value;
                var pesan = document.getElementById('k-pesan').value.trim();
                var teks = 'Assalamu\'alaikum, saya ' + nama + (telp ? ' (' + telp + ')' : '') +
                    '.\nSubjek: ' + subjek + '\n\n' + pesan;
                window.open('https://wa.me/{{ $waDigit }}?text=' + encodeURIComponent(teks), '_blank', 'noopener');
                @endif
            });
        })();
    </script>
@endsection
