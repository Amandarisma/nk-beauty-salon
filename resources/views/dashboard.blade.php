<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Reservasi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(isset($bookings) && $bookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-pink-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Layanan</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $booking->invoice_code }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $booking->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <ul>
                                            @foreach($booking->items as $item)
                                                <li>- {{ $item->treatment->name }} ({{ \Carbon\Carbon::parse($item->scheduled_time)->format('H:i') }})</li>
                                            @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($booking->payment_status == 'paid_dp')
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">Lunas/DP</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">Belum Bayar</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-pink-600">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <p class="text-gray-500 mb-4">Belum ada riwayat booking.</p>
                            <a href="{{ route('home') }}#katalog" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Booking Sekarang</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>