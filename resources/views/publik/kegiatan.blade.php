@extends('layouts.publik')

@section('title', 'Kegiatan — ' . ($masjid->nama_masjid ?? 'Masjid Baitul Mukminin'))

@php $warnaKategori = ['emerald', 'blue', 'amber', 'violet']; @endphp

@section('konten')
    <x-latar-masjid class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('publik.beranda') }}" class="mb-4 inline-flex items-center text-sm text-emerald-300 hover:text-white">
                @svg('heroicon-o-arrow-left', 'mr-2 h-4 w-4') Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-bold md:text-4xl">Program &amp; Kegiatan</h1>
            <p class="mt-2 max-w-2xl text-emerald-100">Agenda kajian, kegiatan sosial, dan program masjid yang akan datang.</p>
        </div>
    </x-latar-masjid>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if ($kegiatan->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach ($kegiatan as $k)
                    @include('publik._kartu-kegiatan', ['k' => $k, 'w' => $warnaKategori[$loop->index % count($warnaKategori)]])
                @endforeach
            </div>
            <div class="mt-8">{{ $kegiatan->links() }}</div>
        @else
            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center text-slate-500">
                Belum ada kegiatan terjadwal.
            </div>
        @endif
    </div>
@endsection
