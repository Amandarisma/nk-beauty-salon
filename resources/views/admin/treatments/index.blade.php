<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tombol Tambah & Pesan Sukses -->
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('admin.treatments.create') }}" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Layanan Baru
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabel Daftar Layanan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($treatments as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-16 h-16 object-cover rounded">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->duration }} Menit</td>
                            <td class="px-6 py-4 text-sm text-pink-600 font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            {{-- <td class="px-6 py-4 text-sm text-pink-600 font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.treatments.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('admin.treatments.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus layanan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>


