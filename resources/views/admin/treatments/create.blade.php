<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Layanan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.treatments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Nama -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Layanan</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Durasi -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Durasi (Menit)</label>
                            <input type="number" name="duration" class="w-full border rounded p-2" required>
                        </div>
                        <!-- Harga -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                            <input type="number" name="price" class="w-full border rounded p-2" required>
                        </div>
                    </div>

                    <!-- Gambar -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto Layanan</label>
                        <input type="file" name="image" class="w-full border rounded p-2">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded">
                            Simpan Layanan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
