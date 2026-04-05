<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ✅ SweetAlert (CUKUP SEKALI) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
        /* Efek blur gelap di background */
        div:where(.swal2-container) {
            background: rgba(17, 24, 39, 0.5) !important;
            backdrop-filter: blur(4px) !important;
        }
        /* Kotak pop-up membulat ala modal baru */
        div:where(.swal2-popup) {
            border-radius: 1.5rem !important; /* rounded-3xl */
            padding: 2.5rem 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid #fdf2f8 !important; /* border-pink-50 */
        }
        /* Judul tebal dan gelap */
        h2:where(.swal2-title) {
            font-weight: 800 !important; /* font-extrabold */
            color: #111827 !important; /* text-gray-900 */
            font-size: 1.5rem !important; /* text-2xl */
            margin-bottom: 0.5rem !important;
        }
        /* Teks deskripsi / detail */
        div:where(.swal2-html-container) {
            color: #6b7280 !important; /* text-gray-500 */
            font-size: 0.875rem !important; /* text-sm */
            margin-top: 0 !important;
        }
        /* Jarak antar tombol */
        div:where(.swal2-actions) {
            margin-top: 2rem !important;
            gap: 0.75rem !important;
            width: 100% !important;
        }
        /* Tombol OK / Simpan (Pink Cetar) */
        button:where(.swal2-confirm) {
            background-color: #db2777 !important; /* bg-pink-600 */
            color: white !important;
            border-radius: 1rem !important; /* rounded-2xl */
            padding: 0.875rem 2rem !important; /* py-3.5 px-8 */
            font-size: 0.875rem !important; /* text-sm */
            font-weight: 700 !important; /* font-bold */
            box-shadow: 0 4px 6px -1px rgba(252, 165, 165, 0.5) !important;
            transition: all 0.2s ease-in-out !important;
        }
        button:where(.swal2-confirm):hover {
            background-color: #be185d !important; /* hover:bg-pink-700 */
            transform: translateY(-2px) !important;
        }
        /* Tombol Batal / Lanjut (Abu-abu Clean) */
        button:where(.swal2-cancel) {
            background-color: white !important;
            color: #6b7280 !important; /* text-gray-500 */
            border: 1px solid #e5e7eb !important; /* border-gray-200 */
            border-radius: 1rem !important; /* rounded-2xl */
            padding: 0.875rem 2rem !important; /* py-3.5 px-8 */
            font-size: 0.875rem !important; /* text-sm */
            font-weight: 700 !important; /* font-bold */
            transition: all 0.2s ease-in-out !important;
        }
        button:where(.swal2-cancel):hover {
            background-color: #f9fafb !important; /* hover:bg-gray-50 */
            color: #374151 !important; /* text-gray-700 */
        }
    </style>

<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">

    @include('layouts.navigation')

    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>

</div>

<!-- ✅ GLOBAL NOTIFICATION (BERSIH) -->
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#e91e63'
    });
});
</script>
@endif

@if(session('alert'))
<script>
document.addEventListener('DOMContentLoaded', function () {

    let alertData = @json(session('alert'));

    let config = {
        icon: alertData.type,
        title: alertData.title,
        text: alertData.message,
        confirmButtonColor: '#db2777',
    };

    if (alertData.context === 'cart') {
        config.confirmButtonText = 'Lihat Keranjang';
        config.showCancelButton = true;
        config.cancelButtonText = 'Lanjut Pilih';

        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('cart.index') }}";
            }
        });
    } else {
        Swal.fire(config);
    }

});
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('alert'))
                let alertData = @json(session('alert'));

                // JIKA ALERT DARI TAMBAH KERANJANG
                if (alertData.context === 'cart_add') {
                    Swal.fire({
                        icon: 'success',
                        title: alertData.title,
                        html: alertData.message + '<br><br><div class="bg-pink-50 p-4 rounded-xl text-sm border border-pink-100 text-left w-max mx-auto font-medium text-gray-700 shadow-inner">📅 ' + alertData.date + '<br>⏰ ' + alertData.time + '</div>',
                        showCancelButton: true,
                        confirmButtonColor: '#db2777', // Warna Pink
                        cancelButtonColor: '#9ca3af',  // Warna Abu-abu
                        confirmButtonText: 'Lanjut ke Keranjang 🛒',
                        cancelButtonText: 'Pilih Layanan Lain',
                        customClass: { popup: 'rounded-3xl' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('cart.index') }}";
                        }
                    });
                } 
                // JIKA ALERT DARI HAPUS KERANJANG
                else if (alertData.context === 'cart_delete') {
                    Swal.fire({
                        icon: 'success',
                        title: alertData.title,
                        text: alertData.message,
                        showCancelButton: true,
                        confirmButtonColor: '#db2777',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Cek Keranjang',
                        cancelButtonText: 'Pilih Layanan Baru',
                        customClass: { popup: 'rounded-3xl' }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // Lempar ke halaman katalog jika milih abu-abu
                            window.location.href = "{{ route('home') }}#katalog";
                        }
                    });
                } 
                // ALERT DEFAULT LAINNYA
                else {
                    Swal.fire({
                        icon: alertData.type,
                        title: alertData.title,
                        text: alertData.message,
                        confirmButtonColor: '#db2777',
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            @endif
        });
    </script>

</body>
</html>