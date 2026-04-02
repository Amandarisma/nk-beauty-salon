<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-pink-600 leading-tight">
            {{ __('Data Pelanggan (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistik CRM Sederhana -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Member Terdaftar</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $customers->count() }} <span class="text-sm font-normal text-gray-400">Orang</span></p>
                    </div>
                    <div class="bg-pink-50 p-3 rounded-2xl text-pink-500">
                        <!-- Icon User Group -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-3xl border border-gray-100">
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-pink-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-l-xl">Profil Pelanggan</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Kontak</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Frekuensi Booking</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Status Member</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-r-xl">Bergabung</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($customers as $customer)
                                <tr class="hover:bg-pink-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <!-- Avatar Inisial -->
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center text-white font-bold shadow-sm">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-800">{{ $customer->name }}</div>
                                                <div class="text-xs text-gray-400 font-mono">ID: {{ $customer->customer_id_code ?? 'CUST-'.$customer->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 flex flex-col">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                {{ $customer->email }}
                                            </span>
                                            <span class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                {{ $customer->phone ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $customer->bookings_count }}x Reservasi
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- LOGIKA KLASIFIKASI CRM -->
                                        @if($customer->bookings_count == 0)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                                New Member
                                            </span>
                                        @elseif($customer->bookings_count >= 5)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 shadow-sm flex items-center gap-1">
                                                👑 VIP / Loyal
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                                Regular
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $customer->created_at->translatedFormat('d M Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
                                        Belum ada data pelanggan yang terdaftar.
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
</x-app-layout>