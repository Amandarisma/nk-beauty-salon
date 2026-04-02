<x-app-layout>
<div class="max-w-3xl mx-auto bg-white p-8 shadow-lg rounded-lg mt-10">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-pink-600">NK Beauty Salon</h1>
        <span class="text-sm text-gray-500">Invoice</span>
    </div>

    <div class="border-b pb-4 mb-4">
        <p><strong>Kode:</strong> {{ $booking->invoice_code }}</p>
        <p><strong>Nama:</strong> {{ $booking->guest_name }}</p>
        <p><strong>Tanggal:</strong> {{ $booking->booking_date }}</p>
    </div>

    <table class="w-full text-sm mb-6">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2">Layanan</th>
                <th class="text-right py-2">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($booking->items as $item)
            <tr class="border-b">
                <td class="py-2">{{ $item->treatment->name }}</td>
                <td class="text-right">Rp {{ number_format($item->price_at_booking, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right text-lg font-bold text-pink-600">
        Total: Rp {{ number_format($booking->total_price, 0, ',', '.') }}
    </div>

    <div class="mt-6 flex justify-between">
        <a href="{{ route('admin.pos.create') }}" 
           class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
           Kembali
        </a>

        <button onclick="window.print()" 
                class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">
            Cetak
        </button>
    </div>

</div>
</x-app-layout>