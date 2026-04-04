<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($carts->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                    <div class="flex justify-center mb-4">
                        <svg class="w-20 h-20 text-pink-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700">Keranjangmu Masih Kosong</h3>
                    <p class="text-gray-500 mt-2">Yuk, pilih perawatan terbaik untukmu sekarang!</p>
                    <a href="{{ route('home') }}#katalog" class="mt-6 inline-block bg-pink-500 text-white px-8 py-3 rounded-full font-bold hover:bg-pink-600 transition shadow-md">
                        Lihat Katalog Layanan
                    </a>
                </div>
            @else
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <div class="lg:w-2/3">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-pink-100">
                            <div class="p-6 bg-white border-b border-gray-100">
                                <h3 class="text-lg font-bold text-pink-600 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                                    Daftar Pesanan
                                </h3>
                                
                                <div class="flow-root">
                                    <ul role="list" class="-my-6 divide-y divide-gray-100">
                                        @php $totalHarga = 0; @endphp
                                        
@foreach($carts as $cart)
                                        @php $totalHarga += $cart->treatment->price; @endphp
                                        <li class="py-6 flex items-center">
                                            
                                            <div class="flex-shrink-0 w-8 h-8 bg-pink-100 text-pink-600 font-extrabold rounded-full flex items-center justify-center mr-4 shadow-sm border border-pink-200">
                                                {{ $loop->iteration }}
                                            </div>

                                            <div class="flex-shrink-0 w-24 h-24 border border-pink-100 rounded-xl overflow-hidden shadow-sm">
                                                @if($cart->treatment->image)
                                                    <img src="{{ asset('storage/' . $cart->treatment->image) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-pink-50 flex items-center justify-center text-xs text-pink-300 font-bold">No Image</div>
                                                @endif
                                            </div>

                                            <div class="ml-4 flex-1 flex flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-bold text-gray-900">
                                                        <h3>{{ $cart->treatment->name }}</h3>
                                                        <p class="ml-4 text-pink-600">Rp {{ number_format($cart->treatment->price, 0, ',', '.') }}</p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500 flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        Durasi: {{ $cart->treatment->duration }} Menit
                                                    </p>
                                                </div>
                                                
                                                <div class="mt-3 text-sm text-gray-600 bg-pink-50 p-3 rounded-xl border border-pink-100 inline-block">
                                                    📅 <span class="font-bold">{{ \Carbon\Carbon::parse($cart->booking_date)->translatedFormat('d F Y') }}</span> | 
                                                    ⏰ <span class="font-bold">{{ \Carbon\Carbon::parse($cart->booking_time)->format('H:i') }} WIB</span>
                                                </div>

                                                <div class="flex-1 flex items-end justify-between text-sm mt-2">
                                                    <p class="text-gray-500"></p>
                                                    <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="flex items-center gap-1 text-red-500 hover:text-red-700 bg-red-50 px-3 py-1.5 rounded-lg transition delete-btn font-bold">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/3">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-pink-100 p-6 sticky top-24">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                            
                            <div class="flex justify-between mb-2 text-gray-600">
                                <span>Total Harga Layanan</span>
                                <span class="font-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="border-t border-gray-100 my-4"></div>
                            
                            <form action="{{ route('checkout.process') }}" method="POST" class="mt-2">
                                @csrf
                                <p class="font-bold text-gray-700 mb-3 text-sm">Pilih Opsi Pembayaran:</p>
                                <div class="space-y-3 mb-5">
                                    <label class="flex items-start gap-3 cursor-pointer p-3 border border-pink-200 rounded-xl hover:bg-pink-50 transition bg-pink-50/50">
                                        <input type="radio" name="payment_type" value="dp" checked class="mt-1 text-pink-600 focus:ring-pink-500" onchange="updateTagihan(this.value)">
                                        <div>
                                            <span class="text-sm font-bold text-gray-800 block">Bayar Uang Muka (DP 30%)</span>
                                            <span class="text-xs text-gray-500 block">Sisa dilunasi di salon.</span>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                        <input type="radio" name="payment_type" value="full" class="mt-1 text-pink-600 focus:ring-pink-500" onchange="updateTagihan(this.value)">
                                        <div>
                                            <span class="text-sm font-bold text-gray-800 block">Bayar Lunas (100%)</span>
                                            <span class="text-xs text-gray-500 block">Bayar penuh sekarang, tinggal perawatan.</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="flex justify-between mb-2 text-gray-800 font-extrabold text-xl">
                                    <span>Wajib Dibayar</span>
                                    <span class="text-pink-600" id="display-tagihan">Rp {{ number_format($totalHarga * 0.3, 0, ',', '.') }}</span>
                                </div>

                                <button type="submit" class="w-full mt-4 bg-pink-600 text-white py-3.5 rounded-xl font-bold hover:bg-pink-700 transition shadow-lg shadow-pink-200 flex justify-center items-center gap-2">
                                    Lanjut Pembayaran
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                            
                            <a href="{{ route('home') }}" class="block text-center mt-5 text-sm text-gray-500 hover:text-pink-600 font-semibold transition">
                                &larr; Tambah Layanan Lain
                            </a>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 🔥 LOGIKA JAVASCRIPT GANTI HARGA
        const totalHargaAsli = {{ $totalHarga ?? 0 }};
        const dpHarga = totalHargaAsli * 0.3;

        function updateTagihan(type) {
            const displayTagihan = document.getElementById('display-tagihan');
            let bayar = type === 'full' ? totalHargaAsli : dpHarga;
            displayTagihan.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(bayar);
        }

document.addEventListener('DOMContentLoaded', function () {
            // 🔥 LOGIKA MENGHAPUS (Beneran Menghapus)
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Yakin mau hapus?',
                        text: "Layanan ini akan dihapus dari keranjangmu.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#db2777', 
                        cancelButtonColor: '#d1d5db',  
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        customClass: { popup: 'rounded-3xl' }
                    }).then((result) => {
                        // KUNCI UTAMANYA DI SINI! Form harus beneran di-submit sayang!
                        if (result.isConfirmed) { form.submit(); }
                    });
                });
            });

            // ... (sisa logika pesan error/alert di bawahnya dibiarkan saja)

            // Hanya menangkap error checkout saja
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ session('error') }}", confirmButtonColor: '#db2777' });
            @endif
        });
    </script>
</x-app-layout>