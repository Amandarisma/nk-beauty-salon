{{-- <x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-bold text-2xl text-pink-600 leading-tight">
                {{ __('Analisis Penggunaan Stok') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Berdasarkan transaksi periode: 
                <!-- Data tanggal dikirim dari Controller -->
                <span class="font-bold text-gray-700">{{ $startDate->translatedFormat('d M') }} - {{ $endDate->translatedFormat('d M Y') }}</span>
            </p>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Card & Legenda Status -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-6 rounded-r-xl shadow-sm">
                <div class="flex items-start gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold text-blue-700 text-lg">Sistem Analisis Otomatis</p>
                        <p class="text-sm text-blue-600">
                            Status stok dihitung otomatis berdasarkan frekuensi layanan yang terjual dalam 7 hari terakhir.
                        </p>
                    </div>
                </div>

                <!-- Legenda Warna Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-blue-100">
                        <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                        <div class="text-sm text-gray-600">
                            <span class="font-bold text-red-600 block">> 10x Seminggu (KRITIS)</span>
                            Indikasi boros, perlu restock segera.
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-blue-100">
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="text-sm text-gray-600">
                            <span class="font-bold text-yellow-600 block">5-10x Seminggu (MENIPIS)</span>
                            Stok mulai berkurang, siapkan order.
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-blue-100">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <div class="text-sm text-gray-600">
                            <span class="font-bold text-green-600 block">< 5x Seminggu (AMAN)</span>
                            Pemakaian normal/sedikit.
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-3xl border border-gray-100">
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-pink-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-l-xl">Layanan / Treatment</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Frekuensi (7 Hari)</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Estimasi Pemakaian</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-r-xl">Status Stok Bahan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($analytics as $item)
                                <tr class="hover:bg-pink-50/30 transition duration-150 border-b border-gray-50">
                                    
                                    <!-- Nama Layanan -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">{{ $item->name }}</div>
                                    </td>

                                    <!-- Jumlah Pemakaian (QTY) -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded inline-block">
                                            {{ $item->usage_count }} kali
                                        </div>
                                    </td>

                                    <!-- Progress Bar Visual -->
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[150px]">
                                            <!-- Panjang bar tergantung jumlah usage, max 15 dianggap 100% -->
                                            @php $percent = min(($item->usage_count / 15) * 100, 100); @endphp
                                            <div class="h-2.5 rounded-full {{ $item->usage_count > 10 ? 'bg-red-500' : ($item->usage_count > 4 ? 'bg-yellow-400' : 'bg-green-500') }}" 
                                                 style="width: {{ $percent }}%"></div>
                                        </div>
                                    </td>

                                    <!-- Status Cerdas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->usage_count > 10)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-700 border border-red-200 animate-pulse">
                                                🚨 Stok Kritis
                                            </span>
                                            <div class="text-[10px] text-red-500 mt-1 font-medium">Segera Restock Bahan!</div>
                                        @elseif($item->usage_count > 4)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                ⚠️ Stok Menipis
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-700 border border-green-200">
                                                ✅ Aman
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                        Belum ada transaksi dalam 7 hari terakhir. Stok dianggap aman.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}