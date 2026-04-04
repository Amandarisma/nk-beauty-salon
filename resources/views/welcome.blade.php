<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NK Beauty Salon</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .modal-open { overflow: hidden; }
        /* Animasi smooth untuk search */
        .treatment-card { transition: all 0.3s ease; }
    </style>
</head>
<body class="antialiased bg-gray-50">

    @include('layouts.navigation')

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
        
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-pink-100 flex items-center justify-center overflow-hidden">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" 
                 src="{{ asset('images/header-bg.jpg') }}" 
                 alt="NK Beauty Salon"
                 onerror="this.onerror=null; this.src='https://placehold.co/800x600/fbcfe8/be185d?text=Foto+Belum+Ada';">
        </div>
    </div>

    <div id="katalog" class="py-12 bg-pink-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-base text-pink-600 font-semibold tracking-wide uppercase">Katalog</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Daftar Layanan Kami
                </p>
                
                <div class="mt-6 max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="searchInput" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-pink-500 focus:ring-pink-500 sm:text-sm shadow-sm" 
                           placeholder="Cari perawatan (misal: Facial, Creambath)...">
                </div>
            </div>

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

    <div id="bookingModal" class="hidden fixed inset-0 z-[9999] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeBookingModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">​</span>
            
            <div class="relative z-50 inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="treatment_id" id="modalTreatmentId">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4" id="modalTitle">Booking Layanan</h3>
                        
                        @php
                            $hasCart = Auth::check() && Auth::user()->carts->count() > 0;
                            $firstCart = $hasCart ? Auth::user()->carts->first() : null;
                        @endphp

                        @if($hasCart)
                            <div class="bg-pink-50 border border-pink-200 p-4 rounded-xl text-sm text-pink-800 mb-4 shadow-sm">
                                <p class="font-bold text-pink-600 mb-1">✨ Digabung ke Jadwal Saat Ini</p>
                                <p>Layanan tambahan ini akan otomatis digabungkan dengan jadwal awal Anda pada:</p>
                                <p class="font-bold mt-2">📅 {{ \Carbon\Carbon::parse($firstCart->booking_date)->translatedFormat('d F Y') }}</p>
                                <p class="font-bold">⏰ {{ \Carbon\Carbon::parse($firstCart->booking_time)->format('H:i') }} WIB</p>
                            </div>
                            <input type="hidden" name="booking_date" value="{{ \Carbon\Carbon::parse($firstCart->booking_date)->format('Y-m-d') }}">
                            <input type="hidden" name="booking_time" value="{{ \Carbon\Carbon::parse($firstCart->booking_time)->format('H:i') }}">
                        @else
                            <div class="mt-2 space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Tanggal</label>
                                    <input type="date" name="booking_date" id="bookingDateInput" min="{{ date('Y-m-d') }}" onchange="checkAvailableSlots()" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-pink-500 focus:border-pink-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Jam Kedatangan</label>
                                    <select name="booking_time" id="bookingTimeSelect" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm" required>
                                        <option value="" disabled selected>-- Pilih Tanggal Dulu --</option>
                                        <option value="10:00">10:00 WIB</option>
                                        <option value="10:30">10:30 WIB</option>
                                        <option value="11:00">11:00 WIB</option>
                                        <option value="11:30">11:30 WIB</option>
                                        <option value="12:00">12:00 WIB</option>
                                        <option value="12:30">12:30 WIB</option>
                                        <option value="13:00">13:00 WIB</option>
                                        <option value="13:30">13:30 WIB</option>
                                        <option value="14:00">14:00 WIB</option>
                                        <option value="14:30">14:30 WIB</option>
                                        <option value="15:00">15:00 WIB</option>
                                        <option value="15:30">15:30 WIB</option>
                                        <option value="16:00">16:00 WIB</option>
                                        <option value="16:30">16:30 WIB</option>
                                        <option value="17:00">17:00 WIB</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-pink-600 text-base font-medium text-white hover:bg-pink-700 focus:outline-none sm:w-auto sm:text-sm">Simpan ke Keranjang</button>
                        <button type="button" onclick="closeBookingModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <section id="tentang" class="py-20 bg-gradient-to-b from-white to-pink-50 border-t border-pink-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900">
                    Tentang NK Beauty Salon
                </h2>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                    Kami hadir untuk memberikan pengalaman perawatan terbaik dengan pelayanan profesional dan suasana nyaman untuk Anda yang ingin tampil percaya diri setiap hari..
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">

                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">🌸 Tentang Kami</h3>
                    <p class="text-gray-600">
                        NK Beauty Salon adalah tempat terbaik untuk merawat dan mempercantik rambut Anda, dengan layanan seperti hair styling, coloring, dan creambath yang dikerjakan oleh tenaga profesional untuk hasil yang sehat, indah, dan tahan lama.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">📞 Hubungi Kami </h3>
                    <div class="space-y-2 text-gray-600">
                        <a href="https://instagram.com/nkbeautysalonjogja" target="_blank" class="block hover:text-pink-500">
                            📸 Instagram: @nkbeautysalonjogja
                        </a>
                        <a href="https://www.tiktok.com/@nk_beautysalonjogja?is_from_webapp=1&sender_device=pc" target="_blank" class="block hover:text-pink-500">
                            📸 tiktok: @nk_beautysalonjogja
                        </a>
                        <a href="mailto:amandarismawati026@gmail.com" class="block hover:text-pink-500">
                            📧 amandarismawati026@gmail.com
                        </a>
                        <a href="https://wa.me/6287889216190" class="block hover:text-pink-500">
                            📞 WhatsApp: 0878-8921-6190
                        </a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">📍 Lokasi</h3>
                    <p class="text-gray-600 mb-3">
                        Jl. Raya Janti No.5, Gowok, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281, Indonesia
                    </p>

                    <iframe 
                        src="https://www.google.com/maps?q=Jl.+Raya+Janti+No.5,+Gowok,+Caturtunggal,+Depok,+Sleman,+Yogyakarta&output=embed"
                        class="w-full h-40 rounded-lg border"
                        allowfullscreen>
                    </iframe>
                </div>

            </div>

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

            <div class="mt-16 text-center">
                <a href="#katalog" class="inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-semibold shadow-md hover:bg-pink-600 transition">
                    Booking Sekarang
                </a>
            </div>

        </div>
    </section>

<script>
    // Membuka Modal
    function openBookingModal(id, name) {
        document.getElementById('modalTreatmentId').value = id;
        document.getElementById('modalTitle').innerText = 'Booking: ' + name;
        document.getElementById('bookingModal').classList.remove('hidden');
    }

    // Menutup Modal
    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
    }

    // 🔥 FUNGSI AJAX: MENGUBAH JAM JADI ABU-ABU
    function checkAvailableSlots() {
        let dateInput = document.getElementById('bookingDateInput').value;
        if(!dateInput) return;

        let timeSelect = document.getElementById('bookingTimeSelect');
        
        // Ganti tulisan sementara saat loading
        timeSelect.options[0].text = "Sedang mengecek jadwal...";

        // Tanya ke backend
        fetch('/api/booked-slots?date=' + dateInput)
        .then(response => response.json())
        .then(blockedSlots => {
            timeSelect.options[0].text = "-- Pilih Jam (10:00 - 17:00) --";

            // Cek satu-satu semua pilihan jam
            for(let i = 1; i < timeSelect.options.length; i++) {
                let option = timeSelect.options[i];
                
                // Jika jam ini masuk di daftar yang diblokir
                if(blockedSlots.includes(option.value)) {
                    option.disabled = true;
                    option.text = option.value + " WIB (Sudah Dipesan)";
                    option.style.backgroundColor = "#e5e7eb"; // warna abu-abu
                    option.style.color = "#9ca3af";
                } else {
                    option.disabled = false;
                    option.text = option.value + " WIB";
                    option.style.backgroundColor = "";
                    option.style.color = "";
                }
            }
        })
        .catch(error => {
            console.error("Error fetching slots:", error);
            timeSelect.options[0].text = "-- Gagal mengecek jadwal --";
        });
    }

    // Logic Pencarian (Search) Katalog
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

        // 🔥 JIKA ALERT DARI TAMBAH KERANJANG (CART_ADD)
        if (alertData.context === 'cart_add') {
            Swal.fire({
                icon: 'success',
                title: alertData.title,
                html: alertData.message + '<br><br><div class="bg-pink-50 p-4 rounded-xl text-sm border border-pink-100 text-left w-max mx-auto font-medium text-gray-700 shadow-inner">📅 ' + alertData.date + '<br>⏰ ' + alertData.time + '</div>',
                showCancelButton: true,
                confirmButtonColor: '#db2777', 
                cancelButtonColor: '#9ca3af',  
                confirmButtonText: 'Lihat Keranjang 🛒',
                cancelButtonText: 'Lanjut Pilih',
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('cart.index') }}";
                }
            });
        } 
        // 🔥 ALERT DEFAULT LAINNYA
        else {
            Swal.fire({
                icon: alertData.type,
                title: alertData.title,
                html: alertData.message, 
                confirmButtonColor: '#db2777',
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if(alertData.context === 'payment') {
                    window.location.href = "{{ route('user.bookings') }}"; 
                } else if(alertData.context === 'booking') {
                    window.location.href = "{{ route('cart.index') }}"; 
                }
            });
        }

    });
    </script>
    @endif

    @if(session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#db2777',
        });
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