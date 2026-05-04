<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.treatments.update', $treatment->id) }}" method="POST" enctype="multipart/form-data" id="treatmentForm">
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
                            
                            <!-- UBAHAN: Input type diubah jadi 'text', dan value di-format dengan pemisah ribuan -->
                            <input type="text" id="inputHarga" value="{{ number_format(round($treatment->price), 0, '', '.') }}" class="w-full border rounded p-2" required>
                            
                            <!-- INPUT HIDDEN: Ini yang akan dikirim ke database (angka asli tanpa titik) -->
                            <input type="hidden" name="price" id="hargaAsli" value="{{ round($treatment->price) }}">
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

    <!-- SCRIPT FORMAT RUPIAH OTOMATIS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputHarga = document.getElementById('inputHarga');
            var hargaAsli = document.getElementById('hargaAsli');

            inputHarga.addEventListener('keyup', function(e) {
                // Hapus semua karakter yang bukan angka
                var value = this.value.replace(/[^,\d]/g, '').toString();
                var split = value.split(',');
                var sisa = split[0].length % 3;
                var rupiah = split[0].substr(0, sisa);
                var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                // Tambahkan titik jika yang di input sudah menjadi angka ribuan
                if (ribuan) {
                    var separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                // Tampilkan format rupiah ke pengguna
                this.value = rupiah;
                
                // Simpan angka murni (tanpa titik) ke input hidden untuk dikirim ke database
                hargaAsli.value = value; 
            });
        });
    </script>
</x-app-layout>