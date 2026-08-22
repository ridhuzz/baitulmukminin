<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Jabatan digambar per jenjang (<em>Tingkat pada bagan</em>) dan diurutkan kiri→kanan sesuai <em>Urutan</em> di menu Jabatan.
            </p>
            <a href="{{ $urlPublik }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50">
                <x-filament::icon icon="heroicon-o-globe-alt" class="h-4 w-4 text-emerald-600" />
                Lihat versi publik
            </a>
        </div>

        @forelse ($blok as $b)
            <x-bagan-struktur :blok="$b" />
        @empty
            <div class="rounded-xl border-2 border-dashed border-slate-200 bg-white py-16 text-center text-slate-500">
                Belum ada entitas aktif.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
