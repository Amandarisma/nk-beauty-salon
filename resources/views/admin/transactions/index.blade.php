<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">Daftar Transaksi</h2>
                    <p class="text-sm text-gray-500">Riwayat global seluruh pembayaran dan reservasi salon yang sudah terlaksana.</p>
                </div>

                <!-- 🔥 TOMBOL DIUBAH MENJADI PEMICU MODAL EXPORT PDF -->
                <button type="button" onclick="document.getElementById('modalExport').classList.remove('hidden')" class="bg-red-500 text-white px-5 py-2.5 rounded-full font-bold hover:bg-red-600 shadow-md flex items-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>

            <!-- Notifikasi Sukses Jika Ada -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-2xl mb-6 flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-50 text-pink-600 text-xs uppercase tracking-wider border-b border-pink-100">
                                <th class="p-4 font-bold rounded-tl-3xl w-16 text-center">No</th>
                                <th class="p-4 font-bold">No. Invoice</th>
                                <th class="p-4 font-bold">Tanggal & Waktu</th>
                                <th class="p-4 font-bold">Pelanggan</th>
                                <th class="p-4 font-bold">Total Harga</th>
                                <th class="p-4 font-bold">Sisa Tagihan</th>
                                <th class="p-4 font-bold text-center">Status Bayar</th>
                                <th class="p-4 font-bold rounded-tr-3xl text-center">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            @forelse ($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    
                                    <td class="p-4 text-center font-semibold text-gray-600 align-middle">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="p-4 align-middle">
                                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md border border-gray-200">
                                            {{ $trx->invoice_code }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 align-middle">
                                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($trx->booking_date)->translatedFormat('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trx->start_time)->format('H:i') }} WIB</p>
                                    </td>
                                    
                                    <td class="p-4 align-middle">
                                        <p class="font-bold text-gray-800">
                                            {{ $trx->user ? $trx->user->name : ($trx->guest_name ?? 'Walk-in') }}
                                        </p>
                                    </td>
                                    
                                    <td class="p-4 font-bold text-gray-800 align-middle">
                                        Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                                    </td>

                                    <td class="p-4 font-bold text-red-500 align-middle">
                                        @php
                                            $sisa = 0;
                                            $nominal_dp = $trx->dp_amount ?? $trx->dp ?? 0; 
                                            $is_walkin = empty($trx->user_id) || ($trx->guest_name != null);

                                            if ($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full' || $is_walkin) {
                                                $sisa = 0; 
                                            } elseif ($trx->payment_status == 'paid_dp') {
                                                $sisa = $trx->total_price - $nominal_dp; 
                                            } else {
                                                $sisa = $trx->total_price; 
                                            }
                                        @endphp
                                        Rp {{ number_format($sisa, 0, ',', '.') }}
                                    </td>

                                    <!-- KOLOM STATUS BAYAR DENGAN ALPINE.JS POP-UP -->
                                    <td class="p-4 text-center align-middle" x-data="{ openModal: false }">
                                        <!-- Tombol Pemicu Pop-up -->
                                        <button type="button" @click="openModal = true" class="hover:opacity-75 transition cursor-pointer focus:outline-none transform hover:scale-105" title="Klik untuk ubah status">
                                            @if($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full')
                                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-200 shadow-sm">Lunas</span>
                                            @elseif($trx->payment_status == 'paid_dp')
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-bold border border-blue-200 shadow-sm">DP Dibayar</span>
                                            @elseif($trx->payment_status == 'cancelled')
                                                <span class="bg-red-100 text-red-700 px-3 py-1.5 rounded-full text-xs font-bold border border-red-200 shadow-sm">Dibatalkan</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-full text-xs font-bold border border-yellow-200 shadow-sm">Menunggu</span>
                                            @endif
                                        </button>

                                        <!-- Pop-up Modal Status -->
                                        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm" x-transition>
                                            <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4 transform transition-all text-left">
                                                <div class="flex justify-between items-center mb-5">
                                                    <h3 class="text-lg font-bold text-gray-800">Ubah Status Bayar</h3>
                                                    <button type="button" @click="openModal = false" class="text-gray-400 hover:text-red-500 focus:outline-none transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                
                                                <p class="text-sm text-gray-500 mb-4 pb-4 border-b border-gray-100">
                                                    Invoice: <span class="font-bold text-pink-600">{{ $trx->invoice_code }}</span>
                                                </p>

                                                <!-- Form Ubah Status -->
                                                <form action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <div class="space-y-3">
                                                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition">
                                                            <div class="flex items-center gap-3">
                                                                <input type="radio" name="payment_status" value="paid_full" class="text-emerald-500 focus:ring-emerald-500 w-4 h-4" {{ ($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full') ? 'checked' : '' }} required>
                                                                <span class="font-bold text-emerald-600">Lunas</span>
                                                            </div>
                                                        </label>
                                                        
                                                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-red-50 hover:border-red-200 transition">
                                                            <div class="flex items-center gap-3">
                                                                <input type="radio" name="payment_status" value="cancelled" class="text-red-500 focus:ring-red-500 w-4 h-4" {{ $trx->payment_status == 'cancelled' ? 'checked' : '' }} required>
                                                                <span class="font-bold text-red-600">Dibatalkan</span>
                                                            </div>
                                                        </label>
                                                    </div>

                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="openModal = false" class="px-4 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2.5 text-sm font-bold text-white bg-pink-600 rounded-xl hover:bg-pink-700 transition shadow-md">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4 text-center align-middle">
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
                                    <td colspan="8" class="p-8 text-center text-gray-400">
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

    <!-- 🔥 MODAL EXPORT PDF DINAMIS (Tambahkan Ini di Bawah div container) 🔥 -->
    <div id="modalExport" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl mx-4 transform transition-all">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-100 text-red-500 rounded-full flex items-center justify-center shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800">Export Laporan PDF</h3>
            </div>
            
            <form action="{{ route('admin.export.pdf') }}" method="GET">
                <div class="space-y-5 mb-8">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Mode Export</label>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center p-3 border border-pink-200 rounded-xl cursor-pointer hover:bg-pink-50 transition">
                            <input type="radio" name="export_mode" value="current" class="text-pink-600 focus:ring-pink-500" checked onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Bulan Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('F Y') }})</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="export_mode" value="specific" class="text-pink-600 focus:ring-pink-500" onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Bulan Tertentu</span>
                        </label>

                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="export_mode" value="range" class="text-pink-600 focus:ring-pink-500" onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Rentang Bulan</span>
                        </label>
                    </div>

                    <!-- Input Bulan Tertentu -->
                    <div id="input-specific" class="hidden animate-fade-in">
                        <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2 mt-2">Pilih Bulan</label>
                        <input type="month" name="specific_month" class="w-full border-gray-200 rounded-xl px-4 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                    </div>

                    <!-- Input Rentang Bulan -->
                    <div id="input-range" class="hidden grid-cols-2 gap-3 animate-fade-in mt-2">
                        <div>
                            <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2">Dari Bulan</label>
                            <input type="month" name="start_month" class="w-full border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2">Sampai Bulan</label>
                            <input type="month" name="end_month" class="w-full border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modalExport').classList.add('hidden')" class="flex-1 py-3 bg-gray-100 rounded-xl font-bold text-gray-500 hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-pink-600 text-white rounded-xl font-bold shadow-lg shadow-pink-200 hover:bg-pink-700 transition">Download</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script untuk Toggle Modal di Halaman Transaksi -->
    <script>
        function toggleExportInputs() {
            const mode = document.querySelector('input[name="export_mode"]:checked').value;
            const specificInput = document.getElementById('input-specific');
            const rangeInput = document.getElementById('input-range');

            specificInput.classList.add('hidden');
            rangeInput.classList.remove('grid');
            rangeInput.classList.add('hidden');

            if (mode === 'specific') {
                specificInput.classList.remove('hidden');
            } else if (mode === 'range') {
                rangeInput.classList.remove('hidden');
                rangeInput.classList.add('grid');
            }
        }
    </script>
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>

</x-app-layout>