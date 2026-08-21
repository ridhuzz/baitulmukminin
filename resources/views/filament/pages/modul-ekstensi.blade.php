<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl space-y-6">
        {{-- Kartu informasi modul --}}
        <div class="overflow-hidden rounded-xl border border-emerald-200 bg-emerald-50/60">
            <div class="flex flex-col gap-6 p-6 md:flex-row md:items-start">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                    <x-filament::icon icon="heroicon-o-squares-plus" class="h-6 w-6" />
                </div>
                <div class="flex-1">
                    <span class="inline-block rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                        Modul Ekstensi &middot; Segera Hadir
                    </span>
                    <h3 class="mt-3 text-lg font-bold text-slate-800">{{ $judul }}</h3>
                    <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-600">{{ $deskripsi }}</p>

                    @if (count($fitur))
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                            @foreach ($fitur as $f)
                                <li class="flex items-start gap-2 text-sm text-slate-700">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />
                                    <span>{{ $f }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pratinjau tabel (kosong) agar tata letak sama dengan modul lain --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col justify-between gap-4 border-b border-slate-100 bg-slate-50/50 p-4 sm:flex-row">
                <div class="relative max-w-md flex-1">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input type="text" disabled placeholder="Cari data..."
                        class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-4 text-sm text-slate-500 disabled:cursor-not-allowed" />
                </div>
                <button type="button" disabled
                    class="flex cursor-not-allowed items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-400">
                    <x-filament::icon icon="heroicon-o-funnel" class="h-4 w-4" /> Filter
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-100 bg-slate-50 font-medium text-slate-500">
                        <tr>
                            @foreach ($kolom as $k)
                                <th class="whitespace-nowrap px-6 py-4">{{ $k }}</th>
                            @endforeach
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="{{ count($kolom) + 1 }}" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-filament::icon icon="heroicon-o-inbox" class="h-6 w-6" />
                                </div>
                                <p class="mt-3 font-medium text-slate-700">Belum ada data</p>
                                <p class="mt-1 text-xs text-slate-500">Modul ini akan diaktifkan pada tahap pengembangan berikutnya.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
