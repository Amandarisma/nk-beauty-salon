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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- SWEETALERT CDN (Agar Pop-up muncul di Dashboard) -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- SCRIPT NOTIFIKASI GLOBAL -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Cek Notifikasi Sukses
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
    }

    else if (alertData.context === 'booking') {
        config.confirmButtonText = 'Lanjut Bayar';
        Swal.fire(config);
    }

    else if (alertData.context === 'payment') {
        config.confirmButtonText = 'Lihat Reservasi';
        config.showCancelButton = true;
        config.cancelButtonText = 'Booking Lagi';

        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('dashboard') }}";
            } else {
                window.location.href = "{{ url('/') }}#katalog";
            }
        });
    }

    else {
        Swal.fire(config);
    }

});
</script>
@endif
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#e91e63'
    });
</script>
@endif
    </body>
</html>