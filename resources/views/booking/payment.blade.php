<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran & Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                <!-- HEADER INVOICE -->
                <div class="p-6 bg-gray-800 text-white flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">INVOICE TAGIHAN</h1>
                        <p class="text-gray-400 text-sm">Kode Booking: {{ $booking->invoice_code }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">Status Pembayaran</p>
                        @if($booking->payment_status == 'pending')
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">BELUM DIBAYAR</span>
                        @else
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">LUNAS / DP OK</span>
                        @endif
                    </div>
                </div>

                <!-- DETAIL ITEM -->
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-gray-600 font-bold mb-2">Rincian Layanan:</h3>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-sm text-gray-500">
                                    <th class="py-2">Layanan</th>
                                    <th class="py-2">Jadwal</th>
                                    <th class="py-2 text-right">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach($booking->items as $item)
                                <tr>
                                    <td class="py-3 font-medium">{{ $item->treatment->name }}</td>
                                    <td class="py-3 text-sm">
                                        {{ \Carbon\Carbon::parse($item->scheduled_date)->translatedFormat('d M Y') }} <br>
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($item->scheduled_time)->format('H:i') }} WIB</span>
                                    </td>
                                    <td class="py-3 text-right">Rp {{ number_format($item->price_at_booking, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- TOTAL HARGA -->
<div class="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-100">
    <div class="flex justify-between text-gray-600 mb-3">
        <span>Total Harga</span>
        <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
    </div>

    <div class="flex justify-between font-extrabold text-lg mb-1">
        @if($booking->dp_amount >= $booking->total_price)
            <span class="text-pink-600">Wajib Bayar (Lunas 100%)</span>
        @else
            <span class="text-pink-600">Wajib Bayar (DP 30%)</span>
        @endif
        
        <span class="text-pink-600">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
    </div>

    @if($booking->dp_amount < $booking->total_price)
        <div class="text-right text-xs text-gray-400 italic">
            *Sisa pembayaran dilunasi saat di salon.
        </div>
    @else
        <div class="text-right text-xs text-emerald-500 font-bold mt-1">
            ✨ Layanan sudah dibayar lunas
        </div>
    @endif
</div>

                    <!-- METODE PEMBAYARAN -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Metode Pembayaran</h3>
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Transfer Bank Manual -->
                            <div class="flex-1 border rounded-lg p-4 hover:border-pink-500 cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 p-2 rounded">🏦</div>
                                    <div>
                                        <h4 class="font-bold">Transfer Bank BCA</h4>
                                        <p class="text-sm text-gray-600">No. Rek: 123-456-7890</p>
                                        <p class="text-sm text-gray-600">A/N: NK Beauty Salon</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Tripay (Logo) -->
                            <div class="flex-1 border rounded-lg p-4 hover:border-pink-500 cursor-pointer transition opacity-50">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 p-2 rounded">💳</div>
                                    <div>
                                        <h4 class="font-bold">E-Wallet / QRIS (Tripay)</h4>
                                        <p class="text-sm text-gray-600">Segera Hadir</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL AKSI -->
                    <div class="mt-8 flex justify-end">
                        <form action="{{ route('booking.pay', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-pink-700 transition shadow-lg flex items-center gap-2">
                                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Konfirmasi Pembayaran (Simulasi)
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
