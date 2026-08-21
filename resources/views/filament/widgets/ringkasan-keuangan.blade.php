@php $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp
<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-2 flex items-center gap-3">
            <div class="rounded-lg bg-slate-100 p-2 text-slate-600">
                <x-filament::icon icon="heroicon-o-wallet" class="h-5 w-5" />
            </div>
            <h3 class="text-sm font-medium text-slate-500">Total Saldo Aktif <span class="font-normal text-slate-400">({{ $labelEntitas }})</span></h3>
        </div>
        <div class="mt-2 text-2xl font-bold text-slate-800">{{ $rp($saldo) }}</div>
    </div>
    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
        <div class="mb-2 flex items-center gap-3">
            <div class="rounded-lg bg-emerald-100 p-2 text-emerald-600">
                <x-filament::icon icon="heroicon-o-arrow-down-right" class="h-5 w-5" />
            </div>
            <h3 class="text-sm font-medium text-emerald-700">Pemasukan Bulan Ini <span class="font-normal text-emerald-600/70">({{ $bulan }})</span></h3>
        </div>
        <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $rp($masuk) }}</div>
    </div>
    <div class="rounded-xl border border-rose-100 bg-rose-50 p-5 shadow-sm">
        <div class="mb-2 flex items-center gap-3">
            <div class="rounded-lg bg-rose-100 p-2 text-rose-600">
                <x-filament::icon icon="heroicon-o-arrow-up-right" class="h-5 w-5" />
            </div>
            <h3 class="text-sm font-medium text-rose-700">Pengeluaran Bulan Ini <span class="font-normal text-rose-600/70">({{ $bulan }})</span></h3>
        </div>
        <div class="mt-2 text-2xl font-bold text-rose-700">{{ $rp($keluar) }}</div>
    </div>
</div>
