@php
    $rp = fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');
@endphp

<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl space-y-6">

        {{-- Aksi cepat --}}
        @if (count($aksiCepat))
            <div class="flex flex-wrap gap-3">
                @foreach ($aksiCepat as $aksi)
                    <a href="{{ $aksi['url'] }}"
                       @class([
                           'flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium shadow-sm transition-colors',
                           'bg-emerald-600 text-white hover:bg-emerald-700' => $aksi['utama'],
                           'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' => ! $aksi['utama'],
                       ])>
                        <x-filament::icon :icon="$aksi['icon']" class="h-4 w-4 {{ $aksi['warna'] }}" />
                        {{ $aksi['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Kartu ringkasan keuangan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-colors hover:border-emerald-300 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-slate-500 transition-colors group-hover:text-emerald-600">Saldo Kas Tunai</h3>
                    <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 transition-colors group-hover:bg-emerald-100">
                        <x-filament::icon icon="heroicon-o-wallet" class="h-5 w-5" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-800">{{ $rp($ringkasan['kas_tunai']) }}</div>
                <div class="mt-2 flex items-center text-xs text-slate-500">
                    <span class="font-medium text-emerald-600">{{ $ringkasan['kas_tunai_ket'] }}</span>
                </div>
            </div>

            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-colors hover:border-blue-300 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-slate-500 transition-colors group-hover:text-blue-600">Saldo Rekening</h3>
                    <div class="rounded-lg bg-blue-50 p-2 text-blue-600 transition-colors group-hover:bg-blue-100">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-800">{{ $rp($ringkasan['rekening']) }}</div>
                <div class="mt-2 flex items-center text-xs text-slate-500">
                    <span class="truncate text-slate-400" title="{{ $ringkasan['rekening_ket'] }}">{{ $ringkasan['rekening_ket'] }}</span>
                </div>
            </div>

            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-colors hover:border-emerald-300 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-slate-500 transition-colors group-hover:text-emerald-600">Pemasukan Bulan Ini</h3>
                    <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 transition-colors group-hover:bg-emerald-100">
                        <x-filament::icon icon="heroicon-o-arrow-trending-up" class="h-5 w-5" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-800">{{ $rp($ringkasan['masuk']) }}</div>
                <div class="mt-2 flex items-center text-xs text-slate-500">
                    @if (! is_null($ringkasan['masuk_persen']))
                        <span class="flex items-center font-medium {{ $ringkasan['masuk_persen'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            <x-filament::icon :icon="$ringkasan['masuk_persen'] >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down'" class="mr-1 h-3 w-3" />
                            {{ $ringkasan['masuk_persen'] >= 0 ? '+' : '' }}{{ $ringkasan['masuk_persen'] }}%
                        </span>
                        <span class="ml-1 text-slate-400">dari bulan lalu</span>
                    @else
                        <span class="text-slate-400">Belum ada pembanding bulan lalu</span>
                    @endif
                </div>
            </div>

            <div class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-colors hover:border-rose-300 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-slate-500 transition-colors group-hover:text-rose-600">Pengeluaran Bulan Ini</h3>
                    <div class="rounded-lg bg-rose-50 p-2 text-rose-600 transition-colors group-hover:bg-rose-100">
                        <x-filament::icon icon="heroicon-o-arrow-trending-down" class="h-5 w-5" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-800">{{ $rp($ringkasan['keluar']) }}</div>
                <div class="mt-2 flex items-center text-xs text-slate-500">
                    @if (! is_null($ringkasan['keluar_persen']))
                        <span class="flex items-center font-medium {{ $ringkasan['keluar_persen'] <= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            <x-filament::icon :icon="$ringkasan['keluar_persen'] <= 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up'" class="mr-1 h-3 w-3" />
                            {{ $ringkasan['keluar_persen'] > 0 ? '+' : '' }}{{ $ringkasan['keluar_persen'] }}%
                        </span>
                        <span class="ml-1 text-slate-400">dari bulan lalu</span>
                    @else
                        <span class="text-slate-400">Belum ada pembanding bulan lalu</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Kolom kiri --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Grafik arus kas --}}
                <div class="dashboard-chart">
                    @livewire(\App\Filament\Widgets\ArusKasChart::class)
                </div>

                {{-- Jadwal imam --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col justify-between gap-4 border-b border-slate-100 bg-slate-50/50 px-5 py-3 sm:flex-row sm:items-center">
                        <h3 class="flex items-center font-semibold text-slate-800">
                            <x-filament::icon icon="heroicon-o-clock" class="mr-2 h-5 w-5 text-emerald-600" />
                            Jadwal Imam
                            <span class="ml-2 text-xs font-normal text-slate-400">{{ \App\Support\Tanggal::lengkap($tanggalJadwal) }}</span>
                        </h3>
                        <div class="flex rounded-lg bg-slate-200/50 p-1">
                            <button type="button" wire:click="setTabJadwal('hari_ini')"
                                @class([
                                    'rounded-md px-4 py-1.5 text-sm font-medium transition-all',
                                    'bg-white text-emerald-700 shadow-sm' => $tabJadwal === 'hari_ini',
                                    'text-slate-500 hover:text-slate-700' => $tabJadwal !== 'hari_ini',
                                ])>Hari Ini</button>
                            <button type="button" wire:click="setTabJadwal('besok')"
                                @class([
                                    'rounded-md px-4 py-1.5 text-sm font-medium transition-all',
                                    'bg-white text-emerald-700 shadow-sm' => $tabJadwal === 'besok',
                                    'text-slate-500 hover:text-slate-700' => $tabJadwal !== 'besok',
                                ])>Besok</button>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100" wire:loading.class="opacity-50">
                        @foreach ($jadwalImam as $baris)
                            @php $aktif = $baris['status'] === 'Berikutnya'; @endphp
                            <div @class([
                                'flex flex-col justify-between gap-4 p-4 transition-colors sm:flex-row sm:items-center',
                                'border-l-4 border-l-emerald-500 bg-emerald-50/50 hover:bg-emerald-50' => $aktif,
                                'hover:bg-slate-50' => ! $aktif,
                            ])>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 shrink-0 text-center">
                                        <div class="mb-1 text-xs font-semibold uppercase tracking-wider {{ $aktif ? 'text-emerald-600' : 'text-slate-400' }}">{{ $baris['nama'] }}</div>
                                        <div class="text-lg font-bold {{ $aktif ? 'text-emerald-700' : 'text-slate-800' }}">{{ $baris['jam'] }}</div>
                                    </div>
                                    <div class="hidden h-10 w-px sm:block {{ $aktif ? 'bg-emerald-200' : 'bg-slate-200' }}"></div>
                                    <div>
                                        <div class="flex items-center text-sm font-medium text-slate-800">
                                            <x-filament::icon icon="heroicon-o-user" class="mr-1.5 h-4 w-4 text-slate-400" />
                                            @if ($baris['imam'])
                                                {{ $baris['imam'] }}
                                            @else
                                                <span class="italic text-slate-400">Imam belum ditentukan</span>
                                            @endif
                                        </div>
                                        <div class="mt-0.5 text-xs text-slate-500">
                                            Muadzin: {{ $baris['muadzin'] ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 self-start sm:self-auto">
                                    @if ($baris['url'])
                                        <a href="{{ $baris['url'] }}" class="text-xs font-medium text-emerald-600 hover:underline">Ubah</a>
                                    @endif
                                    @if ($aktif)
                                        <span class="flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <span class="mr-1.5 h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> Berikutnya
                                        </span>
                                    @elseif ($baris['status'] === 'Terjadwal')
                                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">Terjadwal</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">{{ $baris['status'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kolom kanan --}}
            <div class="space-y-6">
                {{-- Jumat berikutnya --}}
                @php $tagJumat = ($jumat['url'] ?? null) ? 'a' : 'div'; @endphp
                <{{ $tagJumat }} @if ($jumat['url'] ?? null) href="{{ $jumat['url'] }}" @endif
                    class="group relative block overflow-hidden rounded-xl border border-emerald-800 bg-emerald-900 text-white shadow-lg transition-colors hover:bg-emerald-800">
                    <div class="pointer-events-none absolute right-0 top-0 p-4 opacity-10 transition-transform duration-500 group-hover:scale-110">
                        <x-filament::icon icon="heroicon-o-book-open" class="h-24 w-24" />
                    </div>
                    <div class="relative z-10 flex items-center justify-between border-b border-emerald-800/50 px-5 py-4">
                        <h3 class="flex items-center font-semibold">
                            <x-filament::icon icon="heroicon-o-calendar-days" class="mr-2 h-5 w-5 text-emerald-400" />
                            Jumat Berikutnya
                        </h3>
                        <x-filament::icon icon="heroicon-o-chevron-right" class="h-4 w-4 text-emerald-400 transition-transform group-hover:translate-x-1" />
                    </div>
                    <div class="relative z-10 p-5">
                        @if ($jumat)
                            <div class="mb-1 text-sm text-emerald-300">{{ $jumat['hari'] }}, {{ $jumat['tanggal'] }}</div>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-emerald-400">
                                        {{ $jumat['khotib'] && $jumat['imam'] && $jumat['khotib'] !== $jumat['imam'] ? 'Khotib' : 'Khotib & Imam' }}
                                    </div>
                                    <div class="text-lg font-medium leading-tight">{{ $jumat['khotib'] ?? $jumat['imam'] ?? 'Belum ditentukan' }}</div>
                                    @if ($jumat['khotib'] && $jumat['imam'] && $jumat['khotib'] !== $jumat['imam'])
                                        <div class="mt-1 text-sm text-emerald-200">Imam: {{ $jumat['imam'] }}</div>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-4 border-t border-emerald-800/50 pt-4">
                                    <div>
                                        <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-emerald-400">Muadzin</div>
                                        <div class="font-medium">{{ $jumat['muadzin'] ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-emerald-400">Waktu</div>
                                        <div class="font-medium">{{ $jumat['waktu'] ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-emerald-200">Belum ada jadwal Shalat Jumat mendatang. Tambahkan lewat menu Jadwal Ibadah &rsaquo; Jadwal Petugas.</p>
                        @endif
                    </div>
                </{{ $tagJumat }}>

                {{-- Pengumuman --}}
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-4">
                        <h3 class="flex items-center font-semibold text-slate-800">
                            <x-filament::icon icon="heroicon-o-megaphone" class="mr-2 h-5 w-5 text-amber-500" />
                            Pengumuman
                        </h3>
                        @if ($urlPengumuman)
                            <a href="{{ $urlPengumuman }}" class="rounded p-1 text-slate-400 transition-colors hover:text-emerald-600" title="Semua pengumuman">
                                <x-filament::icon icon="heroicon-o-chevron-right" class="h-5 w-5" />
                            </a>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($pengumuman as $p)
                            @php $tagP = $p['url'] ? 'a' : 'div'; @endphp
                            <{{ $tagP }} @if ($p['url']) href="{{ $p['url'] }}" @endif class="group block p-4 transition-colors hover:bg-slate-50">
                                <div class="mb-1 flex items-center gap-2">
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ $p['warna'] }}"></span>
                                    <span class="text-xs font-medium text-slate-500">{{ $p['tanggal'] }}</span>
                                    @unless ($p['publish'])
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Draft</span>
                                    @endunless
                                </div>
                                <h4 class="mb-1 text-sm font-medium text-slate-800 transition-colors group-hover:text-emerald-600">{{ $p['judul'] }}</h4>
                                @if ($p['ringkas'])
                                    <p class="line-clamp-2 text-xs text-slate-600">{{ $p['ringkas'] }}</p>
                                @endif
                            </{{ $tagP }}>
                        @empty
                            <div class="p-6 text-center text-sm text-slate-500">Belum ada pengumuman.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
