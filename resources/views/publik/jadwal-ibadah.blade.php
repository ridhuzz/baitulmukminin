@extends('layouts.publik')

@section('title', 'Jadwal Ibadah — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@php
    use App\Support\Tanggal;
    $hariIni = today();
    $lokasi = trim(($masjid?->kota ?? '') . ($masjid?->provinsi ? ', ' . $masjid->provinsi : ''));
@endphp

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Jadwal Ibadah</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">
                Jadwal shalat {{ $lokasi ?: 'wilayah masjid' }} — {{ Tanggal::lengkapDenganHijriah($hariIni) }}.
                @if ($sumber) Sumber: {{ $sumber }}. @endif
            </p>
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl space-y-12 px-4 py-12 sm:px-6 lg:px-8">

        {{-- Jam shalat hari ini (8 waktu) --}}
        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Hari Ini</h2>
                    <p class="text-slate-500">{{ Tanggal::lengkap($hariIni) }}</p>
                </div>
            </div>
            @if (collect($lengkap)->filter()->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-10 text-center text-slate-500">
                    Jadwal shalat belum tersedia — pengurus perlu mengisi Provinsi &amp; Kab/Kota pada Profil Masjid.
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                    @foreach ($lengkap as $nama => $jam)
                        @php $aktif = $nama === $shalatAktif; $next = $nama === $shalatBerikutnya; @endphp
                        <div class="rounded-2xl border p-4 text-center shadow-sm {{ $aktif ? 'border-emerald-500 bg-emerald-600 text-white' : ($next ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white') }}">
                            <div class="text-[10px] font-bold uppercase tracking-wider {{ $aktif ? 'text-emerald-100' : 'text-slate-400' }}">{{ $nama }}</div>
                            <div class="mt-1 text-2xl font-bold {{ $aktif ? 'text-white' : 'text-slate-800' }}">{{ $jam ?? '--:--' }}</div>
                            @if ($aktif)
                                <div class="mt-1 text-[10px] font-medium text-emerald-100">Sedang berlangsung</div>
                            @elseif ($next)
                                <div class="mt-1 text-[10px] font-medium text-emerald-600">Berikutnya</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            {{-- Jadwal sebulan --}}
            <section class="lg:col-span-2">
                <h2 class="mb-1 text-2xl font-bold text-slate-800">Jadwal Shalat {{ Tanggal::BULAN[$hariIni->month] }} {{ $hariIni->year }}</h2>
                <p class="mb-4 text-sm text-slate-500">Waktu dalam WIB, mengikuti data Kemenag RI untuk {{ $lokasi ?: 'wilayah masjid' }}.</p>
                @if (empty($bulanIni))
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-10 text-center text-slate-500">Data bulanan belum tersedia.</div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-3 py-3 text-center">Imsak</th>
                                    <th class="px-3 py-3 text-center">Subuh</th>
                                    <th class="px-3 py-3 text-center">Terbit</th>
                                    <th class="px-3 py-3 text-center">Dhuha</th>
                                    <th class="px-3 py-3 text-center">Dzuhur</th>
                                    <th class="px-3 py-3 text-center">Ashar</th>
                                    <th class="px-3 py-3 text-center">Maghrib</th>
                                    <th class="px-3 py-3 text-center">Isya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($bulanIni as $tgl => $baris)
                                    @php $c = \Carbon\Carbon::parse($tgl); $isToday = $c->isSameDay($hariIni); $jumat = $c->isFriday(); @endphp
                                    <tr class="{{ $isToday ? 'bg-emerald-50 font-semibold text-emerald-800' : ($jumat ? 'bg-slate-50/60' : '') }}">
                                        <td class="whitespace-nowrap px-4 py-2">
                                            <span class="{{ $jumat ? 'text-emerald-700' : '' }}">{{ Tanggal::hari($c) }}</span>, {{ $c->day }}
                                            @if ($isToday) <span class="ml-1 rounded bg-emerald-600 px-1.5 py-0.5 text-[10px] font-bold uppercase text-white">hari ini</span> @endif
                                        </td>
                                        @foreach (['imsak','subuh','terbit','dhuha','dzuhur','ashar','maghrib','isya'] as $k)
                                            <td class="px-3 py-2 text-center tabular-nums">{{ $baris[$k] ?? '--:--' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Kanan: Jumat & petugas mendatang --}}
            <aside class="space-y-6">
                <div class="overflow-hidden rounded-2xl border border-emerald-800 bg-emerald-900 text-white shadow-lg">
                    <div class="flex items-center border-b border-emerald-800/50 px-5 py-4 font-semibold">
                        @svg('heroicon-o-calendar-days', 'mr-2 h-5 w-5 text-emerald-400') Shalat Jumat Berikutnya
                    </div>
                    <div class="p-5">
                        @if ($jadwalJumat)
                            <div class="text-sm text-emerald-300">{{ Tanggal::lengkap($jadwalJumat->tanggal_jadwal) }}@if ($jadwalJumat->waktu_mulai) · {{ substr($jadwalJumat->waktu_mulai, 0, 5) }} WIB @endif</div>
                            <dl class="mt-4 space-y-3">
                                @foreach (['khotib' => 'Khotib', 'imam' => 'Imam', 'muadzin' => 'Muadzin', 'bilal' => 'Bilal'] as $peran => $label)
                                    @php $d = $jadwalJumat->detail->firstWhere('peran', $peran); @endphp
                                    @if ($d)
                                        <div>
                                            <dt class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400">{{ $label }}</dt>
                                            <dd class="font-medium">{{ $d->petugas->nama ?? '-' }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>
                        @else
                            <p class="text-sm text-emerald-200">Belum ada jadwal Jumat mendatang.</p>
                        @endif
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center border-b border-slate-100 bg-slate-50/50 px-5 py-4 font-semibold text-slate-800">
                        @svg('heroicon-o-user-group', 'mr-2 h-5 w-5 text-emerald-600') Imam &amp; Petugas 7 Hari ke Depan
                    </div>
                    @forelse ($mendatang as $tgl => $daftar)
                        <div class="border-b border-slate-100 px-5 py-3 last:border-b-0">
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ Tanggal::lengkap(\Carbon\Carbon::parse($tgl)) }}</div>
                            <ul class="space-y-1.5 text-sm">
                                @foreach ($daftar as $j)
                                    <li class="flex items-start justify-between gap-3">
                                        <span class="font-medium text-slate-800">{{ $j->jenisIbadah->nama_jenis ?? '-' }}@if ($j->waktu_mulai) <span class="font-normal text-slate-400">· {{ substr($j->waktu_mulai, 0, 5) }}</span>@endif</span>
                                        <span class="text-right text-xs text-slate-500">
                                            {{ $j->detail->map(fn ($d) => ucfirst($d->peran) . ': ' . ($d->petugas->nama ?? '-'))->implode(', ') ?: 'Petugas belum ditentukan' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-slate-500">Belum ada jadwal petugas untuk 7 hari ke depan.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
@endsection
