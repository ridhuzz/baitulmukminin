@props(['blok', 'ringkas' => false])
@php
    use App\Models\Entitas;
    use App\Support\BaganStruktur;
    $e = $blok['entitas'];
    $yayasan = $e->jenis === 'yayasan';
    // [header-bg, header-subtext, avatar, border kartu, badge jabatan, garis penghubung]
    $tema = $yayasan
        ? ['bg-blue-900', 'text-blue-200', 'bg-blue-50 text-blue-700', 'border-blue-200 hover:border-blue-400', 'bg-blue-100 text-blue-700', 'bg-blue-300']
        : ['bg-emerald-900', 'text-emerald-200', 'bg-emerald-50 text-emerald-700', 'border-emerald-200 hover:border-emerald-400', 'bg-emerald-100 text-emerald-700', 'bg-emerald-300'];
    $labelTingkat = [1 => 'Pimpinan', 2 => 'Pengurus Harian', 3 => 'Koordinator / Bidang', 4 => 'Anggota'];
    if ($yayasan) { $labelTingkat = [1 => 'Pembina', 2 => 'Pengawas', 3 => 'Pengurus', 4 => 'Pengurus Harian']; }
@endphp
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-2 px-6 py-5 text-white sm:flex-row sm:items-center sm:justify-between {{ $tema[0] }}">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider {{ $tema[1] }}">{{ Entitas::JENIS[$e->jenis] ?? 'Unit' }}</div>
            <h2 class="text-2xl font-bold">{{ $e->nama }}</h2>
            @if ($e->induk)
                <p class="mt-1 text-sm {{ $tema[1] }}">Di bawah naungan {{ $e->induk->nama }}</p>
            @endif
        </div>
        @if ($blok['struktur'])
            @php $lp = $blok['struktur']->label_periode; @endphp
            <div class="rounded-lg bg-white/10 px-4 py-2 text-sm">
                <div class="text-xs uppercase tracking-wider {{ $tema[1] }}">Periode</div>
                <div class="font-semibold">{{ $blok['struktur']->nama_struktur }}{{ $lp && ! str_contains($blok['struktur']->nama_struktur, $lp) ? ' · ' . $lp : '' }}</div>
            </div>
        @endif
    </div>

    <div class="bg-slate-50/60 p-6">
        @if ($blok['tingkat']->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-slate-200 bg-white py-10 text-center text-sm text-slate-500">
                Belum ada data kepengurusan {{ $e->nama_pendek ?: $e->nama }}.
                @if (! $ringkas)
                    <div class="mt-1 text-xs text-slate-400">Isi lewat menu Profil &amp; Struktur → Data Pengurus → Jabatan &amp; Riwayat Kepengurusan.</div>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center">
                @foreach ($blok['tingkat'] as $tingkat => $kelompokJabatan)
                    @if (! $loop->first)
                        {{-- penghubung antar tingkat --}}
                        <div class="flex flex-col items-center">
                            <div class="h-6 w-px {{ $tema[5] }}"></div>
                            <div class="h-2 w-2 rounded-full {{ $tema[5] }}"></div>
                            <div class="h-4 w-px {{ $tema[5] }}"></div>
                        </div>
                    @endif
                    <div class="w-full">
                        <div class="mb-3 text-center text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
                            {{ $labelTingkat[$tingkat] ?? 'Tingkat ' . $tingkat }}
                        </div>
                        <div class="flex flex-wrap items-stretch justify-center gap-4">
                            @foreach ($kelompokJabatan as $jabatan => $orang)
                                <div class="w-full max-w-[260px] rounded-xl border bg-white p-4 text-center shadow-sm transition-colors sm:w-[220px] {{ $tema[3] }}">
                                    <div class="mb-3 inline-block rounded px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $tema[4] }}">{{ $jabatan }}</div>
                                    <ul class="space-y-3">
                                        @foreach ($orang as $k)
                                            @php $p = $k->pengurus; @endphp
                                            <li class="flex flex-col items-center">
                                                @if ($p?->foto)
                                                    <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama }}" class="h-14 w-14 rounded-full border-2 border-white object-cover shadow">
                                                @else
                                                    <div class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-white text-base font-bold shadow {{ $tema[2] }}">{{ BaganStruktur::inisial($p?->nama) }}</div>
                                                @endif
                                                <div class="mt-2 text-sm font-semibold leading-snug text-slate-800">{{ $p->nama ?? '-' }}</div>
                                                @if (! $ringkas && $p?->telepon)
                                                    <div class="text-xs text-slate-500">{{ $p->telepon }}</div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
