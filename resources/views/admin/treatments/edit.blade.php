<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.treatments.update', $treatment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Layanan</label>
                        <input type="text" name="name" value="{{ $treatment->name }}" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" class="w-full border rounded p-2">{{ $treatment->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Durasi (Menit)</label>
                            <input type="number" name="duration" value="{{ $treatment->duration }}" class="w-full border rounded p-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ $treatment->price }}" class="w-full border rounded p-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Foto (Opsional)</label>
                        @if($treatment->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $treatment->image) }}" class="w-20 h-20 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="image" class="w-full border rounded p-2">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Layanan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
