<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h2 class="font-bold text-2xl text-pink-600 tracking-tight">Data Pelanggan (CRM)</h2>
                <p class="text-sm text-gray-500 mb-2">Manajemen data member dan analisis loyalitas pelanggan.</p>
                
                <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><strong>💡 Petunjuk:</strong> Klik pada <b>Nama Pelanggan</b> atau badge <b>Total Booking</b> untuk melihat riwayat lengkap.</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-pink-50 text-pink-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold rounded-tl-xl rounded-bl-xl">Nama Pelanggan</th>
                            <th class="p-4 font-bold">Email / Kontak</th>
                            <th class="p-4 font-bold text-center">Total Booking</th>
                            <th class="p-4 font-bold">Bergabung Sejak</th>
                            <th class="p-4 font-bold rounded-tr-xl rounded-br-xl">Layanan Favorit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="p-4 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 font-bold flex items-center justify-center text-lg shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="font-bold text-gray-800 hover:text-pink-600 hover:underline transition block">
                                            {{ $customer->name }}
                                        </a>
                                        <p class="text-xs text-gray-400">ID: CUST-{{ $customer->id }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-gray-800">{{ $customer->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $customer->phone ?? '-' }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-100 hover:bg-emerald-100 transition inline-block cursor-pointer">
                                        {{ $customer->bookings_count }}x Reservasi
                                    </a>
                                </td>
                                <td class="p-4 text-gray-600">
                                    {{ \Carbon\Carbon::parse($customer->created_at)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-4">
                                    @if($customer->favorite_treatment != 'Belum ada')
                                        <span class="bg-pink-50 text-pink-600 px-3 py-1.5 rounded-lg text-xs font-bold">
                                            {{ $customer->favorite_treatment }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">Belum ada pelanggan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</x-app-layout>