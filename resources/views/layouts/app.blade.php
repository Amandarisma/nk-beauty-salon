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