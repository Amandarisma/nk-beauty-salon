{{-- // State Management: Inisialisasi Alpine.js 'open: false' untuk mengontrol buka-tutup menu pada tampilan mobile --}}
<nav x-data="{ open: false }" class="bg-white border-b border-pink-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                
                {{-- // Routing Logic: Menggunakan ternary operator. Jika admin, klik logo ke Dashboard; jika user/guest, ke Home (Beranda) --}}
                <div class="shrink-0 flex items-center gap-2 mr-6">
                    <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('home') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold shadow-sm">NK</div>
                        <span class="font-bold text-xl text-gray-800 tracking-tight">NKBeauty<span class="text-pink-500">Salon</span></span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:flex">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            {{-- // Role-Based Access: Menampilkan menu manajemen backend khusus untuk Admin (Layanan, Pelanggan, Transaksi, Stok) --}}
                            {{-- // Visual Feedback: Penggunaan request()->routeIs(...) untuk memberikan garis bawah pink (active state) pada menu yang sedang dibuka membuat UX aplikasi terlihat sangat profesional. --}}
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.treatments.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.treatments.*') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Layanan
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.customers.*') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Pelanggan
                            </a>
                            <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.transactions.*') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Transaksi
                            </a>
                            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.inventory.*') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Stok Barang
                            </a>
                        @else
                            {{-- // Role-Based Access: Menampilkan menu publik dan riwayat reservasi khusus untuk Pelanggan (User) --}}
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Beranda
                            </a>
                            <a href="{{ route('user.bookings') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('user.bookings') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                                Riwayat Reservasi
                            </a>
                        @endif
                    @else
                        {{-- // Guest State: Hanya menampilkan menu beranda jika pengunjung belum melakukan login --}}
                        <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('home') ? 'border-pink-500 text-pink-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-pink-500 hover:border-pink-300' }}">
                            Beranda
                        </a>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    @if(Auth::user()->role === 'user')
                        {{-- // Dynamic Data: Mengecek dan menghitung jumlah item secara dinamis dari relasi database 'carts' milik user yang sedang login --}}
                        <a href="{{ route('cart.index') }}" class="relative transition group flex items-center gap-1 mr-4 {{ request()->routeIs('cart.index') ? 'text-pink-600' : 'text-gray-500 hover:text-pink-600' }}">
                            <div class="relative {{ request()->routeIs('cart.index') ? 'bg-pink-50 p-2 rounded-full ring-2 ring-pink-100' : 'p-2' }} transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{-- // UI/UX: Badge Notifikasi angka keranjang hanya dirender jika ada isinya (> 0) untuk menghindari tampilan angka 0 yang tidak perlu --}}
                                @if(Auth::user()->carts && Auth::user()->carts->count() > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/4 bg-pink-600 border-2 border-white rounded-full shadow-sm">{{ Auth::user()->carts->count() }}</span>
                                @endif
                            </div>
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-pink-600 focus:outline-none transition ease-in-out duration-150">
                                <div class="font-bold">{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- // User Info: Memberikan konfirmasi visual hak akses (Role) kepada pengguna di dalam menu dropdown --}}
                            <div class="block px-4 py-2 text-xs text-gray-500 border-b border-pink-50 mb-1">
                                Hak Akses: <span class="font-bold text-pink-500 uppercase">{{ Auth::user()->role }}</span>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-pink-50 hover:text-pink-600 transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profil Saya') }}
                            </x-dropdown-link>
                            
                            {{-- // Security: Form Logout dieksekusi menggunakan method POST dan directive @csrf untuk mencegah celah keamanan Cross-Site Request Forgery (CSRF) --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-red-500 font-bold hover:text-red-700 hover:bg-red-50 transition border-t border-gray-50 mt-1 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- // Auth Links: Menampilkan tombol Login dan Register jika pengguna belum terautentikasi (Guest) --}}
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-pink-500 font-bold transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 bg-pink-500 text-white rounded-full font-bold hover:bg-pink-600 hover:-translate-y-0.5 transition transform shadow-md">Registrasi</a>
                        @endif
                    </div>
                @endauth
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-pink-500 hover:bg-pink-50 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        {{-- // Responsive Design: Menggunakan directive Alpine.js (:class) untuk menukar ikon hamburger dan tanda silang (X) berdasarkan state 'open' --}}
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>