<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">Daftar Transaksi</h2>
                    <p class="text-sm text-gray-500">Riwayat global seluruh pembayaran dan reservasi salon yang sudah terlaksana.</p>
                </div>

                <a href="{{ route('admin.transactions.pdf') }}" class="bg-red-500 text-white px-5 py-2.5 rounded-full font-bold hover:bg-red-600 shadow-md flex items-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-50 text-pink-600 text-xs uppercase tracking-wider border-b border-pink-100">
                                <th class="p-4 font-bold rounded-tl-3xl">No. Invoice</th>
                                <th class="p-4 font-bold">Tanggal & Waktu</th>
                                <th class="p-4 font-bold">Pelanggan</th>
                                <th class="p-4 font-bold">Total Harga</th>
                                <th class="p-4 font-bold">Sisa Tagihan</th>
                                <th class="p-4 font-bold">Status Bayar</th>
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
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trx->start_time)->format('H:i') }} WIB</p>
                                    </td>
                                    
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">
                                            {{ $trx->user ? $trx->user->name : ($trx->guest_name ?? 'Walk-in') }}
                                        </p>
                                    </td>
                                    
                                    <td class="p-4 font-bold text-gray-800">
                                        Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                                    </td>

                                    <td class="p-4 font-bold text-red-500">
                                        @php
                                            $sisa = 0;
                                            // Mengambil nilai DP, menggunakan default 0 jika kolom tidak ada agar tidak error "undefined"
                                            $nominal_dp = $trx->dp_amount ?? $trx->dp ?? 0; 
                                            
                                            // Cek apakah ini transaksi walk-in (pelanggan tanpa akun/guest)
                                            $is_walkin = empty($trx->user_id) || ($trx->guest_name != null);

                                            if ($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full' || $is_walkin) {
                                                $sisa = 0; // Lunas atau Walk-in, sisa = 0
                                            } elseif ($trx->payment_status == 'paid_dp') {
                                                $sisa = $trx->total_price - $nominal_dp; // Jika DP, kurangi total dengan nominal DP
                                            } else {
                                                $sisa = $trx->total_price; // Jika belum bayar sama sekali
                                            }
                                        @endphp
                                        Rp {{ number_format($sisa, 0, ',', '.') }}
                                    </td>

                                    <td class="p-4">
                                        @if($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200">Lunas</span>
                                        @elseif($trx->payment_status == 'paid_dp')
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">DP Dibayar</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">Menunggu</span>
                                        @endif
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
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Belum ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>