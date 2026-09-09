@php
    use App\Support\Brand;
    use App\Support\MenuPublik;
    $namaMasjid = $masjid->nama_masjid ?? 'Masjid Baitul Mukminin';
    // Menu navigasi dari admin (Pengaturan → Pengaturan Menu); fallback menu bawaan.
    $menuNav = MenuPublik::ambil();
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
                    @foreach ($menuNav as $item)
                        @if (empty($item['anak']))
                            <a href="{{ $item['href'] }}" @if ($item['tab_baru']) target="_blank" rel="noopener" @endif
                               class="text-sm font-medium {{ $item['aktif'] ? 'text-emerald-600' : 'text-slate-600 transition-colors hover:text-emerald-600' }}">{{ $item['label'] }}</a>
                        @else
                            <div class="group relative">
                                <a href="{{ $item['href'] ?? '#' }}"
                                   class="inline-flex items-center gap-1 text-sm font-medium {{ $item['aktif'] ? 'text-emerald-600' : 'text-slate-600 transition-colors group-hover:text-emerald-600' }}">
                                    {{ $item['label'] }}
                                    @svg('heroicon-o-chevron-down', 'h-3.5 w-3.5 transition-transform group-hover:rotate-180')
                                </a>
                                <div class="invisible absolute left-1/2 top-full z-50 -translate-x-1/2 pt-3 opacity-0 transition-all duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                    <div class="w-52 rounded-xl border border-slate-200 bg-white py-2 shadow-lg">
                                        @foreach ($item['anak'] as $sub)
                                            <a href="{{ $sub['href'] }}" @if ($sub['tab_baru']) target="_blank" rel="noopener" @endif
                                               class="block px-4 py-2 text-sm {{ $sub['aktif'] ? 'bg-emerald-50 font-medium text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-600' }}">{{ $sub['label'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
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
    </nav>

    {{-- Menu mobile: drawer dari samping kanan (di luar <nav> karena backdrop-blur mengurung elemen fixed) --}}
        <div id="menu-mobile" class="pointer-events-none fixed inset-0 z-[60] lg:hidden" aria-hidden="true">
            <div id="menu-backdrop" class="absolute inset-0 bg-slate-900/50 opacity-0 transition-opacity duration-300"></div>
            <aside id="menu-panel" class="absolute inset-y-0 right-0 flex w-[82%] max-w-xs translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out" role="dialog" aria-modal="true" aria-label="Menu navigasi">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <span class="flex flex-col leading-tight">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-emerald-600">{{ Brand::BARIS_1 }}</span>
                        <span class="text-sm font-bold text-slate-800">{{ Brand::BARIS_2 }}</span>
                    </span>
                    <button type="button" id="tombol-tutup-menu" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-emerald-600" aria-label="Tutup menu">
                        @svg('heroicon-o-x-mark', 'h-6 w-6')
                    </button>
                </div>
                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    @foreach ($menuNav as $item)
                        <a href="{{ $item['href'] ?? '#' }}" @if ($item['tab_baru']) target="_blank" rel="noopener" @endif
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ $item['aktif'] ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50 hover:text-emerald-600' }}">
                            @svg($item['ikon'], 'h-5 w-5 ' . ($item['aktif'] ? 'text-emerald-600' : 'text-slate-400'))
                            {{ $item['label'] }}
                        </a>
                        @foreach ($item['anak'] as $sub)
                            <a href="{{ $sub['href'] }}" @if ($sub['tab_baru']) target="_blank" rel="noopener" @endif
                               class="ml-4 flex items-center gap-3 rounded-lg border-l-2 py-2 pl-4 pr-3 text-sm {{ $sub['aktif'] ? 'border-emerald-500 bg-emerald-50 font-medium text-emerald-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-emerald-600' }}">
                                {{ $sub['label'] }}
                            </a>
                        @endforeach
                    @endforeach
                </nav>
                <div class="border-t border-slate-200 p-4">
                    <a href="{{ url('/admin/login') }}" class="block rounded-lg bg-emerald-600 px-3 py-2.5 text-center text-sm font-medium text-white hover:bg-emerald-700">
                        Login Pengurus
                    </a>
                </div>
            </aside>
        </div>
        <script>
            (function () {
                var tombol = document.getElementById('tombol-menu');
                var wadah = document.getElementById('menu-mobile');
                if (!tombol || !wadah) return;
                var backdrop = document.getElementById('menu-backdrop');
                var panel = document.getElementById('menu-panel');
                var tutupBtn = document.getElementById('tombol-tutup-menu');
                function setMenu(terbuka) {
                    wadah.classList.toggle('pointer-events-none', !terbuka);
                    wadah.setAttribute('aria-hidden', terbuka ? 'false' : 'true');
                    backdrop.classList.toggle('opacity-0', !terbuka);
                    panel.classList.toggle('translate-x-full', !terbuka);
                    document.body.classList.toggle('overflow-hidden', terbuka);
                    tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
                    tombol.querySelector('.ikon-buka').classList.toggle('hidden', terbuka);
                    tombol.querySelector('.ikon-tutup').classList.toggle('hidden', !terbuka);
                }
                tombol.addEventListener('click', function () { setMenu(panel.classList.contains('translate-x-full')); });
                tutupBtn.addEventListener('click', function () { setMenu(false); });
                backdrop.addEventListener('click', function () { setMenu(false); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
            })();
        </script>

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
