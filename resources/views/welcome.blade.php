<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NK Beauty Salon</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .modal-open { overflow: hidden; }
        /* Animasi smooth untuk search */
        .treatment-card { transition: all 0.3s ease; }
    </style>
</head>
<body class="antialiased bg-gray-50">

    <!-- NAVBAR STICKY -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-pink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">NK</div>
                    <span class="font-bold text-xl text-gray-800 tracking-tight">NKBeauty<span class="text-pink-500">Salon</span></span>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-pink-500 font-medium transition">Beranda</a>
                    <a href="#katalog" class="text-gray-600 hover:text-pink-500 font-medium transition">Layanan</a>
                    <a href="#tentang" class="text-gray-600 hover:text-pink-500 font-medium transition">Tentang Kami</a>

                    @auth
                        <!-- Icon Keranjang -->
                        <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-pink-600 transition group flex items-center gap-1">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @if(Auth::user()->carts->count() > 0)
                                    <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                        {{ Auth::user()->carts->count() }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm font-medium">Keranjang</span>
                        </a>
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 ml-4">Halo, {{ Auth::user()->name }}</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 px-4 py-2 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600 transition shadow-md hover:shadow-lg">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Manjakan Dirimu di</span>
                            <span class="block text-pink-500 xl:inline">NK Beauty Salon</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Solusi kecantikan Anda. Lihat layanan kami dan pesan jadwal Anda secara online dengan mudah dan cepat.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="#katalog" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 md:py-4 md:text-lg transition">
                                    Lihat Layanan
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        
        <!-- GAMBAR HEADER -->
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-pink-100 flex items-center justify-center overflow-hidden">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" 
                 src="{{ asset('images/header-bg.jpg') }}" 
                 alt="NK Beauty Salon"
                 onerror="this.onerror=null; this.src='https://placehold.co/800x600/fbcfe8/be185d?text=Foto+Belum+Ada';">
        </div>
    </div>

    <!-- KATALOG LAYANAN -->
    <div id="katalog" class="py-12 bg-pink-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-base text-pink-600 font-semibold tracking-wide uppercase">Katalog</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Daftar Layanan Kami
                </p>
                
                <!-- SEARCH BAR (FITUR PENCARIAN) -->
                <div class="mt-6 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <!-- Ikon Kaca Pembesar -->
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="searchInput" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-pink-500 focus:ring-pink-500 sm:text-sm shadow-sm" 
                           placeholder="Cari perawatan (misal: Facial, Creambath)...">
                </div>
            </div>

            <!-- Grid Card -->
            <div id="treatmentGrid" class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($treatments as $item)
                <div class="treatment-card flex flex-col rounded-2xl shadow-lg overflow-hidden bg-white hover:shadow-2xl transition duration-300 transform hover:-translate-y-1" data-name="{{ strtolower($item->name) }}">
                    <div class="flex-shrink-0 relative">
                        @if($item->image)
                            <img class="h-56 w-full object-cover" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                        @else
                            <img class="h-56 w-full object-cover" src="https://placehold.co/600x400/e5e7eb/9ca3af?text=No+Image" alt="No Image">
                        @endif
                        <div class="absolute top-0 right-0 bg-pink-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                            {{ $item->duration }} Menit
                        </div>
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 treatment-name">{{ $item->name }}</h3>
                            <p class="mt-3 text-base text-gray-500">{{ Str::limit($item->description, 100) }}</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-lg font-bold text-pink-600">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                            @auth
                                <button onclick="openBookingModal({{ $item->id }}, '{{ addslashes($item->name) }}')" class="px-4 py-2 bg-pink-500 text-white text-sm font-medium rounded-lg hover:bg-pink-600 transition shadow-md">
                                    Masukkan Keranjang
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                                    Login untuk Pesan
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pesan jika tidak ditemukan -->
            <div id="noResults" class="hidden text-center py-10 text-gray-500">
                Yah, perawatan yang kamu cari belum ada nih :(
            </div>

            @if($treatments->isEmpty())
                <div class="text-center py-10 bg-white rounded-lg shadow mt-10">
                    <p class="text-gray-500">Belum ada layanan yang tersedia. Admin, silakan input data dulu ya!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL BOOKING -->
<div id="bookingModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity z-40" onclick="closeBookingModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">​</span>
            
            <div class="relative z-50 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="treatment_id" id="modalTreatmentId">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4" id="modalTitle">Booking Layanan</h3>
                        <div class="mt-2 space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Tanggal</label>
                                <input type="date" name="booking_date" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-pink-500 focus:border-pink-500 shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Jam Kedatangan</label>
                                <select name="booking_time" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm" required>
                                    <option value="" disabled selected>-- Pilih Jam (10:00 - 17:00) --</option>
                                    <option value="10:00">10:00 WIB</option>
                                    <option value="11:00">11:00 WIB</option>
                                    <option value="12:00">12:00 WIB</option>
                                    <option value="13:00">13:00 WIB</option>
                                    <option value="14:00">14:00 WIB</option>
                                    <option value="15:00">15:00 WIB</option>
                                    <option value="16:00">16:00 WIB</option>
                                    <option value="17:00">17:00 WIB</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-pink-600 text-base font-medium text-white hover:bg-pink-700 focus:outline-none sm:w-auto sm:text-sm">Simpan ke Keranjang</button>
                        <button type="button" onclick="closeBookingModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ABOUT + TRUST SECTION -->
<section id="tentang" class="py-20 bg-gradient-to-b from-white to-pink-50 border-t border-pink-100">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <!-- Heading -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-gray-900">
                Tentang NK Beauty Salon
            </h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Kami hadir untuk memberikan pengalaman perawatan terbaik dengan pelayanan profesional dan suasana nyaman untuk kamu yang ingin tampil percaya diri setiap hari..
            </p>
        </div>

        <!-- Grid -->
        <div class="grid md:grid-cols-3 gap-10">

            <!-- About -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                <h3 class="text-xl font-bold text-gray-800 mb-3">🌸 Tentang Kami</h3>
                <p class="text-gray-600">
                    NK Beauty Salon adalah tempat terbaik untuk merawat dan mempercantik rambut kamu, dengan layanan seperti hair styling, coloring, dan creambath yang dikerjakan oleh tenaga profesional untuk hasil yang sehat, indah, dan tahan lama.
                </p>
            </div>

            <!-- Contact -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                <h3 class="text-xl font-bold text-gray-800 mb-3">📞 Hubungi Kami </h3>
                <div class="space-y-2 text-gray-600">
                    <a href="https://instagram.com/nkbeautysalonjogja" target="_blank" class="block hover:text-pink-500">
                        📸 Instagram: @nkbeautysalonjogja
                    </a>
                    <a href="https://www.tiktok.com/@nk_beautysalonjogja?is_from_webapp=1&sender_device=pc" target="_blank" class="block hover:text-pink-500">
                        📸 tiktok: @nk_beautysalonjogja
                    </a>
                    <a href="amandarismawati026@gmail.com" class="block hover:text-pink-500">
                        📧 amandarismawati026@gmail.com
                    </a>
                    <a href="https://wa.me/6287889216190" class="block hover:text-pink-500">
                        📞 WhatsApp: 0878-8921-6190
                    </a>
                </div>
            </div>

            <!-- Location -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                <h3 class="text-xl font-bold text-gray-800 mb-3">📍 Lokasi</h3>
                <p class="text-gray-600 mb-3">
                    Jl. Raya Janti No.5, Gowok, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281, Indonesia
                </p>

                <!-- MAP -->
                <iframe 
                    src="https://www.google.com/maps?q=Jl.+Raya+Janti+No.5,+Gowok,+Caturtunggal,+Depok,+Sleman,+Yogyakarta&output=embed"
                    class="w-full h-40 rounded-lg border"
                    allowfullscreen>
                </iframe>

            </div>

        </div>

        <!-- TRUST SECTION -->
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-bold text-pink-600">1000+</p>
                <p class="text-gray-500 text-sm">Pelanggan Puas</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-pink-600">5⭐</p>
                <p class="text-gray-500 text-sm">Rating Pelayanan</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-pink-600">10+</p>
                <p class="text-gray-500 text-sm">Layanan</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-pink-600">Everyday</p>
                <p class="text-gray-500 text-sm">Open 10:00 - 17:00</p>
            </div>
        </div>

        <!-- CTA -->
        <div class="mt-16 text-center">
            <a href="#katalog" class="inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-semibold shadow-md hover:bg-pink-600 transition">
                Booking Sekarang
            </a>
        </div>

    </div>
</section>

<script>
        function openBookingModal(id, name) {
            document.getElementById('modalTreatmentId').value = id;
            document.getElementById('modalTitle').innerText = 'Booking: ' + name;
            document.getElementById('bookingModal').classList.remove('hidden');
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        // Logic Pencarian (Search)
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let cards = document.querySelectorAll('.treatment-card');
            let hasResult = false;

            cards.forEach(card => {
                let name = card.getAttribute('data-name');
                if (name.includes(filter)) {
                    card.style.display = ""; // Tampilkan
                    hasResult = true;
                } else {
                    card.style.display = "none"; // Sembunyikan
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            document.getElementById('noResults').style.display = hasResult ? "none" : "block";
        });
    </script>

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

        // 🔥 CART
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

        // 🔥 BOOKING
        else if (alertData.context === 'booking') {
            config.confirmButtonText = 'Lanjut Bayar';

            Swal.fire(config).then(() => {
                window.location.href = "{{ route('cart.index') }}";
            });
        }

        // 🔥 PAYMENT
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

    <footer class="bg-white border-t border-gray-200 mt-10">
    <div class="max-w-7xl mx-auto px-6 py-6 text-center text-gray-500 text-sm">
        © {{ date('Y') }} NK Beauty Salon X Amandarisma
    </div>
</footer>

</body>
</html>