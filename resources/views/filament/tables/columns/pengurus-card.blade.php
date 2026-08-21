@php
    /** @var \App\Models\Pengurus $record */
    $record = $getRecord();
    $inisial = collect(explode(' ', trim($record->nama)))
        ->filter(fn ($k) => preg_match('/^[A-Za-z]/', $k))
        ->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))
        ->take(2)
        ->implode('');
    $aktif = $record->kepengurusan
        ->where('status_aktif', true)
        ->sortBy(fn ($k) => ($k->struktur?->entitas?->urutan ?? 9) * 100 + ($k->jabatan->urutan ?? 99));
    $struktur = $aktif->first()?->struktur;
    $periode = $struktur?->label_periode ?: '—';
    $warnaEntitas = ['yayasan' => 'bg-blue-50 text-blue-700', 'masjid' => 'bg-emerald-50 text-emerald-600', 'lainnya' => 'bg-slate-100 text-slate-600'];
@endphp
<div class="relative flex w-full flex-col items-center p-5 text-center">
    @if ($record->foto)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($record->foto) }}" alt="{{ $record->nama }}"
             class="mb-3 h-20 w-20 rounded-full border-4 border-white object-cover shadow-sm">
    @else
        <div class="mb-3 flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-emerald-100 text-2xl font-bold text-emerald-700 shadow-sm">
            {{ $inisial ?: 'P' }}
        </div>
    @endif
    <h3 class="text-lg font-bold text-slate-800">{{ $record->nama }}</h3>
    <div class="mb-4 mt-1 flex flex-wrap items-center justify-center gap-1.5">
        @forelse ($aktif as $k)
            @php $jenis = $k->struktur?->entitas?->jenis ?? 'lainnya'; @endphp
            <span class="flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $warnaEntitas[$jenis] ?? $warnaEntitas['lainnya'] }}">
                <x-filament::icon icon="heroicon-o-shield-check" class="mr-1 h-3 w-3" />
                @if ($k->struktur?->entitas)
                    <span class="mr-1 opacity-70">{{ $k->struktur->entitas->label }} ·</span>
                @endif
                {{ $k->jabatan->nama_jabatan ?? '-' }}
            </span>
        @empty
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Belum ada jabatan aktif</span>
        @endforelse
    </div>
    <div class="mt-2 w-full space-y-2 border-t border-slate-100 pt-4 text-sm text-slate-600">
        <div class="flex items-center justify-between">
            <span class="flex items-center"><x-filament::icon icon="heroicon-o-phone" class="mr-2 h-4 w-4 text-slate-400" /> Telepon</span>
            <span class="font-medium text-slate-800">{{ $record->telepon ?: '—' }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-400">Periode</span>
            <span class="font-medium text-slate-800">{{ $periode }}</span>
        </div>
        <div class="flex items-center justify-between pt-2">
            <span class="text-slate-400">Status</span>
            <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $record->status_aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                {{ $record->status_aktif ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>
</div>
