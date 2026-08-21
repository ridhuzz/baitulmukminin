@php
    $sekarang = now();
@endphp
<div class="ms-auto flex items-center gap-2 sm:gap-4">
    <div class="hidden items-center rounded-full border border-slate-200 bg-slate-100/80 px-3 py-1.5 text-sm font-medium text-slate-600 lg:flex">
        <x-filament::icon icon="heroicon-o-calendar-days" class="me-2 h-4 w-4 text-emerald-600" />
        {{ \App\Support\Tanggal::lengkapDenganHijriah($sekarang) }}
    </div>
    <div class="mx-1 hidden h-6 w-px bg-slate-200 lg:block"></div>
    <a href="{{ url('/') }}" target="_blank" rel="noopener"
       class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-emerald-600"
       title="Buka halaman publik">
        <x-filament::icon icon="heroicon-o-globe-alt" class="h-5 w-5" />
        <span class="hidden sm:inline">Situs Publik</span>
    </a>
</div>
