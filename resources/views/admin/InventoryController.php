<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-pink-600 leading-tight">
            {{ __('Analisis Stok Barang') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistik Ringkas (Pastel) -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Item Barang</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $items->count() }} <span class="text-sm font-normal text-gray-400">Jenis</span></p>
                    </div>
                    <div class="bg-pink-50 p-3 rounded-2xl text-pink-500">
                        <!-- Icon Box -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-3xl border border-gray-100">
                <div class="p-8">
                    
                    <!-- Notifikasi Sukses -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-2" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="block sm:inline font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-pink-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-l-xl">Nama Barang</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Stok Saat Ini</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider">Update Terakhir</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold text-pink-500 uppercase tracking-wider rounded-r-xl">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($items as $item)
                                <tr class="hover:bg-pink-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">{{ $item->item_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Logika Warna Stok: Merah jika < 10, Hijau jika aman -->
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $item->stock < 10 ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-green-100 text-green-600 border border-green-200' }}">
                                            {{ $item->stock }}
                                        </span>
                                        @if($item->stock < 10)
                                            <span class="ml-2 text-xs text-red-500 font-bold animate-pulse">! Menipis</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($item->last_updated)->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Form Update Cepat -->
                                        <form action="{{ route('admin.inventory.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="stock" value="{{ $item->stock }}" class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-pink-500 focus:border-pink-500 transition shadow-sm" min="0">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-md hover:shadow-lg">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
                                        Belum ada data stok barang. Silakan tambahkan lewat Database.
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