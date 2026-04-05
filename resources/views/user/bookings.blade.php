<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h2 class="font-bold text-2xl text-pink-600 tracking-tight">Riwayat Reservasi Saya</h2>
                <p class="text-sm text-gray-500">Pantau jadwal kedatangan, status layanan, dan detail pembayaranmu di sini.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-2 sm:p-6">
                @if(isset($bookings) && $bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-pink-50 text-pink-600 text-xs uppercase tracking-wider border-b border-pink-100">
                                    <th class="p-4 font-bold rounded-tl-xl rounded-bl-xl">No. Invoice</th>
                                    <th class="p-4 font-bold">Jadwal Kedatangan</th>
                                    <th class="p-4 font-bold">Layanan</th>
                                    <th class="p-4 font-bold text-center">Status</th>
                                    <th class="p-4 font-bold rounded-tr-xl rounded-br-xl text-right">Rincian Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                                @foreach($bookings as $booking)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="p-4 align-top">
                                            <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md border border-gray-200">
                                                {{ $booking->invoice_code }}
                                            </span>
                                        </td>
                                        
                                        <td class="p-4 align-top">
                                            <p class="font-bold text-pink-600 text-base">
                                                {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d F Y') }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">
                                                Dipesan: {{ $booking->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </td>

                                        <td class="p-4 align-top text-gray-600">
                                            <ul class="space-y-1">
                                                @foreach($booking->items as $item)
                                                    <li class="flex items-center gap-2">
                                                        <span class="text-pink-400 text-xs">●</span> 
                                                        <span>{{ $item->treatment->name }} <b class="text-gray-800">({{ \Carbon\Carbon::parse($item->scheduled_time)->format('H:i') }})</b></span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <td class="p-4 align-top text-center">
                                            @if($booking->booking_status == 'cancelled')
                                                <span class="inline-block bg-red-50 text-red-600 border border-red-200 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                                    Dibatalkan
                                                </span>
                                            @elseif($booking->booking_status == 'completed')
                                                <span class="inline-block bg-gray-100 text-gray-500 border border-gray-200 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                                    Selesai
                                                </span>
                                            @elseif($booking->payment_status == 'pending')
                                                <div class="flex flex-col gap-2 items-center">
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="inline-block bg-yellow-50 text-yellow-600 border border-yellow-200 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-yellow-500 hover:text-white transition whitespace-nowrap cursor-pointer">
                                                        Bayar Sekarang &rarr;
                                                    </a>
                                                    
                                                    <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="cancel-booking-form m-0">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="text-[11px] font-bold text-gray-400 hover:text-red-500 transition underline cancel-booking-btn">
                                                            Batalkan Reservasi
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="inline-block bg-emerald-50 text-emerald-600 border border-emerald-200 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm">
                                                    Terjadwal
                                                </span>
                                            @endif
                                        </td>

                                        <td class="p-4 align-top text-right">
                                            <div class="flex flex-col text-sm inline-block">
                                                <span class="text-xs text-gray-500 flex justify-between gap-4">
                                                    <span>Total Asli:</span> 
                                                    <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                </span>
                                                <span class="text-xs text-gray-500 border-b border-gray-100 pb-1 mb-1 flex justify-between gap-4">
                                                    <span>DP Masuk:</span> 
                                                    <span class="text-emerald-600">- Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                                                </span>
                                                <span class="font-extrabold text-pink-600 text-base flex justify-between gap-4">
                                                    <span>Sisa:</span> 
                                                    <span>Rp {{ number_format($booking->total_price - $booking->dp_amount, 0, ',', '.') }}</span>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-pink-50 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-pink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Belum ada riwayat reservasi</h3>
                        <p class="text-gray-500 mb-6 text-sm">Ayo manjakan dirimu dengan berbagai layanan terbaik kami!</p>
                        <a href="{{ route('home') }}#katalog" class="inline-block bg-pink-600 text-white px-8 py-3 rounded-full font-bold hover:bg-pink-700 shadow-md transition transform hover:-translate-y-1">
                            Booking Sekarang
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 🔥 LOGIKA POP-UP KONFIRMASI BATAL BOOKING
            const cancelBookingBtns = document.querySelectorAll('.cancel-booking-btn');
            cancelBookingBtns.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('.cancel-booking-form');
                    
                    Swal.fire({
                        title: 'Batalkan Reservasi?',
                        text: "Jadwal ini akan dibatalkan dan tidak bisa dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // Warna merah untuk tombol YA
                        cancelButtonColor: '#9ca3af',  // Warna abu-abu untuk batal
                        confirmButtonText: 'Ya, Batalkan!',
                        cancelButtonText: 'Tidak, Kembali',
                        customClass: { popup: 'rounded-3xl' } 
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // LOGIKA PESAN SUKSES
            @if(session('alert'))
                let alertData = @json(session('alert'));
                Swal.fire({
                    icon: alertData.type,
                    title: alertData.title,
                    text: alertData.message,
                    confirmButtonColor: '#db2777',
                    customClass: { popup: 'rounded-3xl' }
                });
            @endif
        });
    </script>
</x-app-layout>