@php
    $user = filament()->auth()->user();
    $nama = $user?->name ?? 'Pengguna';
    $inisial = collect(explode(' ', trim($nama)))
        ->filter()
        ->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))
        ->take(2)
        ->implode('');
    $peran = ($user && method_exists($user, 'getRoleNames')) ? $user->getRoleNames()->first() : null;
@endphp
<div class="shrink-0 border-t border-emerald-800/50 bg-emerald-900/50 p-4">
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-emerald-600 bg-emerald-700 font-bold text-white shadow-inner">
            {{ $inisial ?: 'U' }}
        </div>
        <div class="flex-1 overflow-hidden">
            <p class="truncate text-sm font-medium text-white">{{ $nama }}</p>
            <p class="truncate text-xs text-emerald-300">{{ $peran ?? 'Pengurus' }}</p>
        </div>
        <form method="POST" action="{{ filament()->getLogoutUrl() }}">
            @csrf
            <button type="submit" title="Keluar"
                class="shrink-0 rounded-md p-2 text-emerald-300 transition-colors hover:bg-emerald-800 hover:text-white">
                <x-filament::icon icon="heroicon-o-arrow-right-on-rectangle" class="h-4 w-4" />
            </button>
        </form>
    </div>
</div>
