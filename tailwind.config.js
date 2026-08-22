/** Tailwind untuk halaman publik (landing, pengumuman, laporan). */
export default {
    content: [
        './resources/views/layouts/**/*.blade.php',
        './resources/views/publik/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './app/Http/Controllers/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
