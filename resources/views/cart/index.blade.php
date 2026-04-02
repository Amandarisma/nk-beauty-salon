<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Cek apakah keranjang kosong? -->
            @if($carts->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                    <div class="flex justify-center mb-4">
                        <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700">Keranjangmu Masih Kosong</h3>
                    <p class="text-gray-500 mt-2">Yuk, pilih perawatan terbaik untukmu sekarang!</p>
                    <a href="{{ route('home') }}#katalog" class="mt-6 inline-block bg-pink-500 text-white px-6 py-2 rounded-full font-bold hover:bg-pink-600 transition">
                        Lihat Katalog Layanan
                    </a>
                </div>
            @else
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- KOLOM KIRI: DAFTAR ITEM -->
                    <div class="lg:w-2/3">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Pesanan</h3>
                                
                                <div class="flow-root">
                                    <ul role="list" class="-my-6 divide-y divide-gray-200">
                                        @php $totalHarga = 0; @endphp
                                        
                                        @foreach($carts as $cart)
                                        @php $totalHarga += $cart->treatment->price; @endphp
                                        <li class="py-6 flex">
                                            <!-- Foto Kecil -->
                                            <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                                @if($cart->treatment->image)
                                                    <img src="{{ asset('storage/' . $cart->treatment->image) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-xs text-gray-400">No Image</div>
                                                @endif
                                            </div>

                                            <div class="ml-4 flex-1 flex flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h3>{{ $cart->treatment->name }}</h3>
                                                        <p class="ml-4 text-pink-600">Rp {{ number_format($cart->treatment->price, 0, ',', '.') }}</p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">Durasi: {{ $cart->treatment->duration }} Menit</p>
                                                </div>
                                                
                                                <!-- Detail Jadwal -->
                                                <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                                    📅 Tanggal: <span class="font-semibold">{{ \Carbon\Carbon::parse($cart->booking_date)->translatedFormat('d F Y') }}</span> <br>
                                                    ⏰ Jam: <span class="font-semibold">{{ \Carbon\Carbon::parse($cart->booking_time)->format('H:i') }} WIB</span>
                                                </div>

                                                <div class="flex-1 flex items-end justify-between text-sm">
                                                    <p class="text-gray-500"></p> <!-- Spacer -->

                                                    <!-- Tombol Hapus -->
<form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="delete-form">
    @csrf
    @method('DELETE')
    <button type="button" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 delete-btn">
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

                    <!-- KOLOM KANAN: RINGKASAN PEMBAYARAN -->
                    <div class="lg:w-1/3">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sticky top-24">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                            
                            <div class="flex justify-between mb-2 text-gray-600">
                                <span>Total Harga Layanan</span>
                                <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="border-t border-gray-200 my-4"></div>
                            
                            <div class="flex justify-between mb-2 text-gray-800 font-bold text-lg">
                                <span>Total Tagihan</span>
                                <span class="text-pink-600">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-4 bg-yellow-50 border border-yellow-200 p-3 rounded text-sm text-yellow-800">
                                <p><strong>Info Pembayaran:</strong></p>
                                <p>Anda perlu membayar DP (Uang Muka) sebesar <strong>30%</strong> untuk mengamankan jadwal.</p>
                                <p class="mt-1 font-bold">DP yang harus dibayar: Rp {{ number_format($totalHarga * 0.3, 0, ',', '.') }}</p>
                            </div>

                            <!-- Tombol Checkout -->
                            <form action="{{ route('checkout.process') }}" method="POST" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition shadow-lg flex justify-center items-center gap-2">
                                    Lanjut Pembayaran
                                    <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                            
                            <a href="{{ route('home') }}" class="block text-center mt-4 text-sm text-gray-500 hover:text-gray-700">
                                &larr; Kembali Pilih Layanan
                            </a>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
<script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const form = this.closest('.delete-form');
                        Swal.fire({
                            title: 'Yakin mau hapus?',
                            text: "Data yang dihapus tidak bisa dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#db2777', 
                            cancelButtonColor: '#d1d5db',  
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal',
                            reverseButtons: true 
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
        </x-app-layout> ```
