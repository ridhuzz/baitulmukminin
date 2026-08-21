@php
    use App\Support\EntitasAktif;
    $daftar = EntitasAktif::daftar();
    $aktifId = EntitasAktif::id();
    $dipaksa = EntitasAktif::dipaksa();
@endphp
@if ($daftar->isNotEmpty())
    <div class="flex items-center gap-2">
        <span class="hidden text-xs font-semibold uppercase tracking-wider text-slate-400 md:inline">Entitas</span>
        @if ($dipaksa)
            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700">
                <x-filament::icon icon="heroicon-o-lock-closed" class="me-1.5 h-3.5 w-3.5" />
                {{ $dipaksa->label }}
            </span>
        @else
            <div class="flex rounded-lg bg-slate-200/60 p-1">
                @foreach ($daftar as $e)
                    <a href="{{ route('entitas.ganti', $e->id) }}"
                       @class([
                           'rounded-md px-3 py-1 text-sm font-medium transition-all',
                           'bg-white text-emerald-700 shadow-sm' => $aktifId === $e->id,
                           'text-slate-500 hover:text-slate-700' => $aktifId !== $e->id,
                       ])>{{ $e->label }}</a>
                @endforeach
                <a href="{{ route('entitas.ganti', 'semua') }}"
                   @class([
                       'rounded-md px-3 py-1 text-sm font-medium transition-all',
                       'bg-white text-emerald-700 shadow-sm' => $aktifId === null,
                       'text-slate-500 hover:text-slate-700' => $aktifId !== null,
                   ])>Semua</a>
            </div>
        @endif
    </div>
@endif
