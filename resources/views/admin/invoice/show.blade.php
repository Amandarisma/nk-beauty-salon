<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print-hidden">
            {{ __('Invoice Reservasi') }} #{{ $booking->invoice_code }}
        </h2>
    </x-slot>

    <style>
        @media print {
            body { background-color: white !important; }
            /* Sembunyikan navbar, header, dan elemen dengan class print-hidden saat dicetak */
            nav, header, .print-hidden { display: none !important; }
            /* Hilangkan bayangan dan jarak berlebih agar pas di kertas struk */
            .shadow-sm { box-shadow: none !important; }
            .py-12 { padding: 0 !important; }
            .sm\:px-6, .lg\:px-8 { padding: 0 !important; margin: 0 !important; }
            .bg-white { border: none !important; }
        }
    </style>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-100">
                
                <div class="flex justify-between border-b-2 border-pink-100 pb-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-extrabold text-pink-600 tracking-tight">NK Beauty Salon</h1>
                        <p class="text-gray-500 text-sm mt-1">Jl. Raya Janti No.5, Yogyakarta</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-2xl tracking-widest text-gray-200">INVOICE</p>
                        <p class="text-sm font-bold text-gray-700 mt-1">{{ $booking->invoice_code }}</p>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->created_at)->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                <div class="mb-8 bg-gray-50 p-4 rounded-lg inline-block min-w-[50%]">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kepada Yth.:</p>
                    <p class="text-xl font-bold text-gray-900">{{ $booking->user->name ?? $booking->guest_name }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->user->phone ?? '-' }}</p>
                </div>

                <table class="w-full text-left mb-8">
                    <thead>
                        <tr class="bg-pink-50 border-b border-pink-100">
                            <th class="py-3 px-4 font-bold text-gray-700 rounded-tl-lg">Layanan</th>
                            <th class="py-3 px-4 font-bold text-gray-700 text-center">Jadwal</th>
                            <th class="py-3 px-4 text-right font-bold text-gray-700 rounded-tr-lg">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 text-gray-800 font-medium">{{ $item->treatment->name }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500 text-center">
                                {{ \Carbon\Carbon::parse($item->scheduled_date)->format('d/m/Y') }} <br>
                                <span class="font-bold text-pink-500">({{ \Carbon\Carbon::parse($item->scheduled_time)->format('H:i') }})</span>
                            </td>
                            <td class="py-4 px-4 text-right text-gray-800 font-medium">Rp {{ number_format($item->price_at_booking, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end">
                    <div class="w-2/3 sm:w-1/2">
                        <div class="flex justify-between font-black text-xl border-t-2 border-pink-200 pt-4">
                            <span class="text-gray-800">Total Lunas:</span>
                            <span class="text-pink-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-16 text-center text-gray-500 text-sm italic">
                    <p>Terima kasih atas kunjungan Anda ke NK Beauty Salon!</p>
                    <p>Semoga harimu semakin cantik dan menyenangkan 💅✨</p>
                </div>

                <div class="mt-10 text-center print-hidden border-t border-gray-200 pt-8">
                    <button onclick="window.print()" class="bg-pink-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-pink-700 transition transform hover:-translate-y-1">
                        🖨️ Cetak Struk
                    </button>
                    <a href="{{ route('admin.pos.create') }}" class="ml-6 text-gray-500 hover:text-pink-600 font-medium transition">
                        ← Kembali ke Kasir
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>