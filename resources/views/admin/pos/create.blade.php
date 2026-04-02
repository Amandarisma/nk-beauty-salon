<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Transaksi Offline (Walk-in)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.pos.store') }}" method="POST">
                    @csrf
                    
                    <!-- Pilihan 1: Member Lama -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cari Member (Opsional)</label>
                        <select name="user_id" class="w-full border rounded p-2">
                            <option value="">-- Bukan Member / Tamu Baru --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->customer_id_code ?? 'ID-'.$user->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan 2: Tamu Baru -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tamu (Jika bukan member)</label>
                        <input type="text" name="guest_name" class="w-full border rounded p-2" placeholder="Contoh: Ibu Rina">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                            <input type="date" name="date" class="w-full border rounded p-2" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jam</label>
                            <input type="time" name="time" class="w-full border rounded p-2" value="{{ date('H:i') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Layanan</label>
                        <select name="treatment_id" class="w-full border rounded p-2" required>
                            @foreach($treatments as $treatment)
                                <option value="{{ $treatment->id }}">{{ $treatment->name }} - Rp {{ number_format($treatment->price) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-2 px-4 rounded hover:bg-purple-700">
                        Simpan Transaksi & Cetak
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>