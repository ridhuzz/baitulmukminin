@php
    $namaMasjid = $masjid->nama_masjid ?? 'Masjid Baitul Mukminin';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $namaMasjid . ' — SIMASJID')</title>
    <meta name="description" content="@yield('deskripsi', 'Portal informasi jamaah ' . $namaMasjid . ': jadwal shalat, kegiatan, dan transparansi keuangan masjid.')">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    {{-- Navigasi --}}
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <a href="{{ route('publik.beranda') }}" class="flex items-center gap-2">
                    @svg('heroicon-o-building-office-2', 'h-8 w-8 text-emerald-600')
                    <span class="text-xl font-bold tracking-wide text-slate-800">SIMASJID</span>
                </a>
                <div class="hidden items-center space-x-8 md:flex">
                    <a href="{{ route('publik.beranda') }}" class="text-sm font-medium {{ request()->routeIs('publik.beranda') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Beranda</a>
                    <a href="{{ route('publik.beranda') }}#jadwal" class="text-sm font-medium text-slate-600 transition-colors hover:text-emerald-600">Jadwal Ibadah</a>
                    <a href="{{ route('publik.kegiatan') }}" class="text-sm font-medium {{ request()->routeIs('publik.kegiatan') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Kegiatan</a>
                    <a href="{{ route('publik.struktur') }}" class="text-sm font-medium {{ request()->routeIs('publik.struktur') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Struktur</a>
                    <a href="{{ route('publik.laporan') }}" class="text-sm font-medium {{ request()->routeIs('publik.laporan') ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">Transparansi</a>
                </div>
                <div class="flex items-center">
                    <a href="{{ url('/admin/login') }}"
                       class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                        Login Pengurus
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @yield('konten')

    {{-- Footer --}}
    <footer class="bg-slate-900 py-12 text-slate-400">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            @svg('heroicon-o-building-office-2', 'mx-auto mb-4 h-10 w-10 text-emerald-600')
            <h3 class="mb-2 text-xl font-bold tracking-wide text-white">SIMASJID</h3>
            <p class="mx-auto mb-6 max-w-md text-sm leading-relaxed">
                Sistem Informasi dan Manajemen Masjid terpadu untuk memudahkan jamaah mengakses informasi terkini secara transparan.
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
