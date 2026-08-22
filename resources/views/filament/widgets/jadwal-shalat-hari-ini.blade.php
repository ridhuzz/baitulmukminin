<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-2 border-b border-slate-100 bg-slate-50/50 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="flex items-center font-semibold text-slate-800">
            <x-filament::icon icon="heroicon-o-clock" class="mr-2 h-5 w-5 text-emerald-600" />
            Jam Shalat Hari Ini
            <span class="ml-2 text-xs font-normal text-slate-400">{{ $tanggal }}</span>
        </h3>
        <div class="text-xs text-slate-500">
            @if ($sumber)
                Sumber: <span class="font-medium text-slate-700">{{ $sumber }}</span>{{ $lokasi ? ' · ' . $lokasi : '' }}
            @elseif ($urlProfil)
                <span class="text-amber-600">Provinsi & Kab/Kota belum diisi —</span>
                <a href="{{ $urlProfil }}" class="font-medium text-emerald-600 hover:underline">isi di Profil Masjid</a>
            @else
                Provinsi & Kab/Kota masjid belum diisi.
            @endif
        </div>
    </div>
    <div class="grid grid-cols-4 divide-x divide-slate-100 md:grid-cols-8">
        @foreach ($waktu as $nama => $jam)
            @php $aktif = $nama === $berikutnya; @endphp
            <div class="px-2 py-3 text-center {{ $aktif ? 'bg-emerald-50' : '' }}">
                <div class="text-[10px] font-semibold uppercase tracking-wider {{ $aktif ? 'text-emerald-600' : 'text-slate-400' }}">{{ $nama }}</div>
                <div class="text-base font-bold {{ $aktif ? 'text-emerald-700' : 'text-slate-800' }}">{{ $jam ?? '--:--' }}</div>
            </div>
        @endforeach
    </div>
</div>
