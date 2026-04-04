<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">Detail Pelanggan</h2>
                    <p class="text-sm text-gray-500">Riwayat reservasi untuk Mbak/Ibu {{ $customer->name }}</p>
                </div>
                <a href="{{ route('admin.customers.index') }}" class="bg-white border border-gray-200 text-gray-600 px-5 py-2 rounded-full font-bold hover:bg-gray-100 shadow-sm transition">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-6 mb-8 flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-pink-100 text-pink-600 font-bold flex items-center justify-center text-3xl shadow-inner">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h3>
                    <div class="flex gap-4 mt-2 text-sm text-gray-600">
                        <span class="flex items-center gap-1"><span class="text-pink-500">📧</span> {{ $customer->email }}</span>
                        <span class="flex items-center gap-1"><span class="text-pink-500">📱</span> {{ $customer->phone ?? 'Belum ada nomor' }}</span>
                        <span class="flex items-center gap-1"><span class="text-pink-500">📅</span> Member sejak {{ \Carbon\Carbon::parse($customer->created_at)->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <h3 class="font-bold text-lg text-gray-800 mb-4 ml-2">Histori Transaksi</h3>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-50 text-pink-600 text-xs uppercase tracking-wider border-b border-pink-100">
                                <th class="p-4 font-bold rounded-tl-3xl">No. Invoice</th>
                                <th class="p-4 font-bold">Tanggal</th>
                                <th class="p-4 font-bold">Layanan</th>
                                <th class="p-4 font-bold">Total Harga</th>
                                <th class="p-4 font-bold rounded-tr-3xl text-center">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            @forelse ($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="p-4">
                                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md border border-gray-200">
                                            {{ $trx->invoice_code }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($trx->booking_date)->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="p-4">
                                        @foreach($trx->items as $item)
                                            <div class="text-xs text-gray-600">• {{ $item->treatment->name }}</div>
                                        @endforeach
                                    </td>
                                    <td class="p-4 font-bold text-gray-800">
                                        Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('admin.invoice', $trx->id) }}" class="inline-flex items-center justify-center bg-pink-100 text-pink-600 p-2 rounded-xl hover:bg-pink-500 hover:text-white transition" title="Lihat Struk/Invoice">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>