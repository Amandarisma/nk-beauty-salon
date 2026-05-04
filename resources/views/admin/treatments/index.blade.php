<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-bold text-2xl text-pink-600 tracking-tight">Kelola Layanan</h2>
                    <p class="text-sm text-gray-500">Daftar treatment, harga, dan durasi pengerjaan salon.</p>
                </div>
                <a href="{{ route('admin.treatments.create') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2.5 px-5 rounded-full shadow-md flex items-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Layanan
                </a>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-2xl mb-6 flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-pink-50 text-pink-600 text-xs uppercase tracking-wider border-b border-pink-100">
                                <!-- TAMBAHAN: Kolom No dengan sudut melengkung -->
                                <th class="p-4 font-bold rounded-tl-3xl w-16 text-center">No</th>
                                <!-- UBAHAN: Class rounded-tl-3xl dihapus dari sini -->
                                <th class="p-4 font-bold w-24 text-center">Foto</th>
                                <th class="p-4 font-bold">Nama & Deskripsi Layanan</th>
                                <th class="p-4 font-bold text-center">Durasi</th>
                                <th class="p-4 font-bold text-right">Harga</th>
                                <th class="p-4 font-bold rounded-tr-3xl text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            @forelse($treatments as $item)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    
                                    <!-- TAMBAHAN: Isi nomor -->
                                    <td class="p-4 align-top pt-6 text-center font-semibold text-gray-600">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="p-4 flex justify-center">
                                        @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" class="w-14 h-14 object-cover rounded-xl shadow-sm border border-gray-200">
                                        @else
                                            <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 border border-gray-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="p-4 align-top pt-5 w-1/3">
                                        <p class="font-bold text-gray-800 text-base">{{ $item->name }}</p>
                                        @if($item->description)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed" title="{{ $item->description }}">
                                                {{ $item->description }}
                                            </p>
                                        @else
                                            <p class="text-[10px] text-gray-400 mt-1 italic">Belum ada deskripsi.</p>
                                        @endif
                                    </td>

                                    <td class="p-4 align-top pt-5 text-center">
                                        <span class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200">
                                            {{ $item->duration }} Menit
                                        </span>
                                    </td>

                                    <td class="p-4 align-top pt-5 text-right font-extrabold text-pink-600 text-base">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>

                                    <td class="p-4 align-top pt-5 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.treatments.edit', $item->id) }}" class="bg-blue-50 text-blue-600 p-2 rounded-xl hover:bg-blue-500 hover:text-white transition" title="Edit Layanan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                            
                                            <form action="{{ route('admin.treatments.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah kamu yakin ingin menghapus layanan ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 p-2 rounded-xl hover:bg-red-500 hover:text-white transition" title="Hapus Layanan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <!-- UBAHAN: colspan dari 5 jadi 6 -->
                                    <td colspan="6" class="p-8 text-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Belum ada data layanan salon.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>