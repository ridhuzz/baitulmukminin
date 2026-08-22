{{--
    Latar foto Masjid Baitul Mukminin + overlay gradasi (sama dengan hero beranda),
    untuk header halaman publik. Tinggi mengikuti isi; foto di-"cover" & fokus ke kubah.
    Pakai: <x-latar-masjid class="py-14"> ... </x-latar-masjid>
--}}
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-emerald-950 text-white']) }}>
    <img src="{{ asset('images/masjid-baitul-mukminin.jpg') }}" alt=""
         aria-hidden="true"
         class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover"
         style="object-position: 58% 36%;">
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 via-emerald-950/60 to-emerald-950/35"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/60 via-transparent to-emerald-950/30"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.05]" style="background-image:url('{{ asset('images/pattern-gold.svg') }}');background-size:128px 128px;"></div>
    <div class="relative z-10">
        {{ $slot }}
    </div>
</div>
