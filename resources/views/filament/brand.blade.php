@php use App\Support\Brand; @endphp
<span class="brand-simasjid flex items-center gap-2.5">
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-800/60 ring-1 ring-emerald-600/40">
        <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5 text-emerald-300" />
    </span>
    <span class="flex min-w-0 flex-col leading-snug">
        <span class="text-[9px] font-semibold uppercase tracking-[0.14em] text-emerald-300">{{ Brand::BARIS_1 }}</span>
        <span class="text-[13px] font-bold text-white">{{ Brand::BARIS_2 }}</span>
    </span>
</span>
