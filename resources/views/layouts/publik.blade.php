@php
    use App\Support\Brand;
    $namaMasjid = $masjid->nama_masjid ?? 'Masjid Baitul Mukminin';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $namaMasjid . ' — ' . Brand::NAMA)</title>
    <meta name="description" content="@yield('deskripsi', 'Portal informasi jamaah ' . $namaMasjid . ': jadwal shalat, kegiatan, dan transparansi keuangan masjid.')">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>.font-arab{font-family:'Amiri','Scheherazade New','Traditional Arabic',serif;}</style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    {{-- Navigasi --}}
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <a href="{{ route('publik.beranda') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                        @svg('heroicon-o-building-office-2', 'h-5 w-5')
                    </span>
                    <span class="flex flex-col leading-tight">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-600">{{ Brand::BARIS_1 }}</span>
                        <span class="text-sm font-bold text-slate-800 sm:text-base">{{ Brand::BARIS_2 }}</span>
                    </span>
                </a>
                <div class="hidden items-center space-x-8 lg:flex">
                    <a href="{{ route('publik.beranda') }}" class="text-sm font-medium {{ request()->routeIs('publik.beranda') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Beranda</a>
                    <a href="{{ route('publik.jadwal') }}" class="text-sm font-medium {{ request()->routeIs('publik.jadwal') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Jadwal Ibadah</a>
                    <a href="{{ route('publik.kegiatan') }}" class="text-sm font-medium {{ request()->routeIs('publik.kegiatan') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Kegiatan</a>
                    <a href="{{ route('publik.struktur') }}" class="text-sm font-medium {{ request()->routeIs('publik.struktur') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Struktur</a>
                    <a href="{{ route('publik.laporan') }}" class="text-sm font-medium {{ request()->routeIs('publik.laporan') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Transparansi</a>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ url('/admin/login') }}"
                       class="hidden rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 sm:inline-flex sm:px-5">
                        Login Pengurus
                    </a>
                    {{-- Tombol menu (mobile & tablet) --}}
                    <button type="button" id="tombol-menu" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 hover:text-emerald-600 lg:hidden"
                            aria-controls="menu-mobile" aria-expanded="false" aria-label="Buka menu">
                        @svg('heroicon-o-bars-3', 'h-6 w-6 ikon-buka')
                        @svg('heroicon-o-x-mark', 'hidden h-6 w-6 ikon-tutup')
                    </button>
                </div>
            </div>
        </div>
        {{-- Menu mobile --}}
        <div id="menu-mobile" class="hidden border-t border-slate-200 bg-white lg:hidden">
            <div class="mx-auto max-w-7xl space-y-1 px-4 py-3 sm:px-6">
                @foreach ([
                    'publik.beranda' => 'Beranda',
                    'publik.jadwal' => 'Jadwal Ibadah',
                    'publik.kegiatan' => 'Kegiatan',
                    'publik.struktur' => 'Struktur',
                    'publik.laporan' => 'Transparansi',
                ] as $rute => $label)
                    <a href="{{ route($rute) }}"
                       class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($rute) ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50 hover:text-emerald-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <a href="{{ url('/admin/login') }}" class="mt-2 block rounded-lg bg-emerald-600 px-3 py-2.5 text-center text-sm font-medium text-white hover:bg-emerald-700 sm:hidden">
                    Login Pengurus
                </a>
            </div>
        </div>
        <script>
            (function () {
                var tombol = document.getElementById('tombol-menu');
                var menu = document.getElementById('menu-mobile');
                if (!tombol || !menu) return;
                tombol.addEventListener('click', function () {
                    var terbuka = menu.classList.toggle('hidden') === false;
                    tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
                    tombol.setAttribute('aria-label', terbuka ? 'Tutup menu' : 'Buka menu');
                    tombol.querySelector('.ikon-buka').classList.toggle('hidden', terbuka);
                    tombol.querySelector('.ikon-tutup').classList.toggle('hidden', !terbuka);
                });
            })();
        </script>
    </nav>

    @yield('konten')

    {{-- Footer --}}
    <footer class="relative overflow-hidden bg-slate-900 py-12 text-slate-400">
        <div class="pointer-events-none absolute inset-0 opacity-[0.06]" style="background-image:url('{{ asset('images/pattern-islamic.svg') }}');background-size:96px 96px;"></div>
        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <div class="font-arab mb-3 text-2xl text-emerald-300/90" dir="rtl" lang="ar">{{ Brand::NAMA_ARAB }}</div>
            <h3 class="mb-1 text-lg font-bold tracking-wide text-white">{{ Brand::BARIS_2 }}</h3>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-400">{{ Brand::BARIS_1 }}</p>
            <p class="mx-auto mb-6 max-w-md text-sm leading-relaxed">
                Sistem informasi dan manajemen masjid terpadu untuk memudahkan jamaah mengakses informasi terkini secara transparan.
            </p>
            @if (!empty($masjid?->alamat) || !empty($masjid?->kontak))
                <p class="mx-auto mb-6 max-w-xl text-xs leading-relaxed text-slate-500">
                    {{ $masjid->alamat }}@if(!empty($masjid->kota)), {{ $masjid->kota }}@endif
                    @if (!empty($masjid->kontak)) &middot; {{ $masjid->kontak }} @endif
                </p>
            @endif
            <div class="border-t border-slate-800 pt-6 text-sm">
                &copy; {{ date('Y') }} {{ $namaMasjid }}. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>
</body>
</html>
