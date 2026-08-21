@php
    use App\Support\Tanggal;
    $w = $w ?? 'emerald';
    $kelas = [
        'emerald' => ['hover:border-emerald-300', 'bg-emerald-50 text-emerald-800', 'bg-emerald-100 text-emerald-700', 'group-hover:text-emerald-600', 'text-emerald-500'],
        'blue' => ['hover:border-blue-300', 'bg-blue-50 text-blue-800', 'bg-blue-100 text-blue-700', 'group-hover:text-blue-600', 'text-blue-500'],
        'amber' => ['hover:border-amber-300', 'bg-amber-50 text-amber-800', 'bg-amber-100 text-amber-700', 'group-hover:text-amber-600', 'text-amber-500'],
        'violet' => ['hover:border-violet-300', 'bg-violet-50 text-violet-800', 'bg-violet-100 text-violet-700', 'group-hover:text-violet-600', 'text-violet-500'],
    ][$w];
    $mulai = $k->tanggal_mulai;
    $selesai = $k->tanggal_selesai;
    $pic = $k->pic->map(fn ($p) => $p->pengurus->nama ?? null)->filter()->implode(', ');
@endphp
<div class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all sm:flex-row {{ $kelas[0] }}">
    <div class="flex w-full shrink-0 flex-col items-center justify-center border-b border-slate-100 p-6 sm:w-32 sm:border-b-0 sm:border-r lg:w-40 {{ $kelas[1] }}">
        @if ($mulai)
            <span class="mb-1 text-sm font-semibold uppercase tracking-wider">{{ Tanggal::hari($mulai) }}</span>
            <span class="text-4xl font-bold">{{ $mulai->day }}</span>
            <span class="mt-1 text-sm font-medium">{{ Tanggal::BULAN[$mulai->month] }}</span>
        @else
            <span class="text-sm font-semibold">Segera</span>
        @endif
    </div>
    <div class="flex-1 p-6">
        <span class="mb-3 inline-block rounded px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $kelas[2] }}">
            {{ $k->kategori->nama_kategori ?? 'Kegiatan' }}
        </span>
        <h3 class="mb-2 text-xl font-bold text-slate-800 transition-colors {{ $kelas[3] }}">{{ $k->nama_kegiatan }}</h3>
        <div class="mt-4 space-y-2 text-sm text-slate-600">
            <p class="flex items-center">
                @svg('heroicon-o-clock', 'mr-2 h-4 w-4 ' . $kelas[4])
                @if ($mulai)
                    {{ $mulai->format('H:i') }}@if ($selesai) – {{ $selesai->format('H:i') }}@endif WIB
                @else
                    Waktu menyusul
                @endif
            </p>
            @if ($pic)
                <p class="flex items-center">@svg('heroicon-o-user', 'mr-2 h-4 w-4 ' . $kelas[4]) {{ $pic }}</p>
            @endif
            @if ($k->lokasi)
                <p class="flex items-center">@svg('heroicon-o-map-pin', 'mr-2 h-4 w-4 ' . $kelas[4]) {{ $k->lokasi }}</p>
            @endif
        </div>
    </div>
</div>
