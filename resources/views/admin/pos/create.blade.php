<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Transaksi Offline (Walk-in)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Ada kesalahan saat mengisi data:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.pos.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cari Member (Opsional)</label>
                        <select name="user_id" class="w-full border rounded p-2">
                            <option value="">-- Bukan Member / Tamu Baru --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->phone ?? 'Tidak ada HP' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 bg-pink-50 p-4 rounded-lg border border-pink-100">
                        <p class="text-sm text-pink-600 font-bold mb-2">Isi jika tamu BUKAN member:</p>
                        <div class="mb-3">
                            <label class="block text-gray-700 text-sm font-bold mb-1">Nama Tamu</label>
                            <input type="text" name="guest_name" class="w-full border rounded p-2" placeholder="Contoh: Ibu Rina">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">No HP</label>
                            <input type="text" name="phone" class="w-full border rounded p-2" placeholder="08xxxxxxx">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal (Hari Ini)</label>
                            <input type="date" name="date" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed text-gray-500" value="{{ date('Y-m-d') }}" readonly required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jam Kedatangan (WIB)</label>
                            
                            <input type="hidden" name="time" id="realTime" value="{{ date('H:i') }}">
                            
                            <div class="flex items-center space-x-2">
                                <select id="hourSelect" onchange="updateTimeValue()" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-pink-500 focus:border-pink-500 text-center font-medium bg-white">
                                    @for($i = 0; $i <= 23; $i++)
                                        @php $h = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $h }}" {{ date('H') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                    @endfor
                                </select>
                                
                                <span class="font-bold text-gray-700">:</span>
                                
                                <select id="minuteSelect" onchange="updateTimeValue()" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-pink-500 focus:border-pink-500 text-center font-medium bg-white">
                                    @for($i = 0; $i <= 59; $i++)
                                        @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $m }}" {{ date('i') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Layanan (Bisa centang lebih dari 1)</label>
                        
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="searchTreatment" class="w-full pl-9 border border-gray-300 rounded-lg p-2 text-sm focus:ring-pink-500 focus:border-pink-500 bg-gray-50" placeholder="Ketik nama layanan untuk mencari cepat...">
                        </div>

                        <div class="border rounded-lg p-4 bg-gray-50 max-h-60 overflow-y-auto grid grid-cols-1 gap-2" id="treatmentContainer">
                            @foreach($treatments as $treatment)
                                <label class="treatment-item flex items-center space-x-3 bg-white p-3 rounded shadow-sm cursor-pointer hover:border-pink-300 border border-transparent transition" data-name="{{ strtolower($treatment->name) }}">
                                    <input type="checkbox" name="treatment_ids[]" value="{{ $treatment->id }}" class="form-checkbox h-5 w-5 text-pink-600 rounded focus:ring-pink-500">
                                    <div class="flex-1">
                                        <span class="text-gray-800 font-bold block">{{ $treatment->name }}</span>
                                        <span class="text-gray-500 text-xs">{{ $treatment->duration }} Menit</span>
                                    </div>
                                    <span class="text-pink-600 font-bold text-sm">Rp {{ number_format($treatment->price, 0, ',', '.') }}</span>
                                </label>
                            @endforeach
                            
                            <div id="noTreatmentResult" class="hidden text-center py-4 text-gray-500 text-sm">
                                Layanan tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-pink-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-pink-700 transition">
                        Simpan Transaksi & Cetak Invoice
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        // 🔥 INI FUNGSI YANG HILANG TADI: Biar jam-nya kesimpan di sistem!
        function updateTimeValue() {
            let hour = document.getElementById('hourSelect').value;
            let minute = document.getElementById('minuteSelect').value;
            document.getElementById('realTime').value = hour + ':' + minute;
        }

        // Fungsi Pencarian Layanan
        document.getElementById('searchTreatment').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.treatment-item');
            let hasResult = false;

            items.forEach(item => {
                let name = item.getAttribute('data-name');
                if (name.includes(filter)) {
                    item.style.display = "flex";
                    hasResult = true;
                } else {
                    item.style.display = "none";
                }
            });

            document.getElementById('noTreatmentResult').style.display = hasResult ? "none" : "block";
        });
    </script>
</x-app-layout>