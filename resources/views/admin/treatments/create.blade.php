<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Layanan Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.treatments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ERROR --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <strong>Oops! Cek lagi isianmu:</strong>
                            <ul class="list-disc pl-5 mt-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- NAMA --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Layanan</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border rounded p-2" required>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" class="w-full border rounded p-2">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        {{-- DURASI --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Durasi (Menit)</label>
                            <input type="number" name="duration" value="{{ old('duration') }}"
                                   class="w-full border rounded p-2" required>
                        </div>

                        {{-- HARGA DENGAN FORMAT TITIK OTOMATIS --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                            <!-- Input yang terlihat (pakai type="text" agar bisa dikasih titik) -->
                            <input type="text" id="price_display" inputmode="numeric" placeholder="Contoh: 150.000"
                                   class="w-full border rounded p-2" required>
                            
                            <!-- Input tersembunyi yang dikirim ke database (angka asli tanpa titik) -->
                            <input type="hidden" name="price" id="price_actual" value="{{ old('price') }}">
                        </div>

                    </div>

                    {{-- FOTO --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto Layanan</label>
                        <input type="file" name="image" class="w-full border rounded p-2">
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                            class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded transition">
                            Simpan Layanan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- SCRIPT UNTUK FORMAT TITIK OTOMATIS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const displayInput = document.getElementById('price_display');
            const actualInput = document.getElementById('price_actual');

            // Fungsi untuk menambahkan titik ribuan
            function formatRupiah(angka) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            // Jika ada old('price') dari validasi yang error, tampilkan format titiknya
            if (actualInput.value) {
                displayInput.value = formatRupiah(actualInput.value);
            }

            // Event listener saat user mengetik
            displayInput.addEventListener('keyup', function(e) {
                // Format nilai yang terlihat di layar
                this.value = formatRupiah(this.value);
                
                // Simpan nilai aslinya (tanpa titik) ke input hidden untuk dikirim ke backend
                actualInput.value = this.value.replace(/\./g, '');
            });
        });
    </script>
</x-app-layout>