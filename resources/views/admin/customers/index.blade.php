<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-pink-600 leading-tight">
            {{ __('Data Pelanggan (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-pink-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-pink-500 uppercase tracking-wider">Nama Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-pink-500 uppercase tracking-wider">Email / Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-pink-500 uppercase tracking-wider">Total Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-pink-500 uppercase tracking-wider">Bergabung Sejak</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-pink-500 uppercase tracking-wider">Layanan Favorit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customers as $customer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-bold">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">ID: CUST-{{ $customer->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $customer->bookings_count }}x Reservasi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $customer->created_at->format('d M Y') }}
<td class="px-4 py-2">
    @if($customer->favorite_treatment)
        <span class="bg-pink-100 text-pink-600 px-2 py-1 rounded text-xs">
            {{ ucfirst(strtolower($customer->favorite_treatment)) ?? '-' }}
        </span>
    @else
        -
    @endif
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>