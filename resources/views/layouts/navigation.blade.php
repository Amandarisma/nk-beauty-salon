<nav x-data="{ open: false }" class="bg-white border-b border-pink-100 sticky top-0 z-50 shadow-sm">

    <!-- NAVBAR -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex">

                <!-- LOGO -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                       class="flex items-center gap-2 transition transform hover:scale-105">
                        <div class="w-9 h-9 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                            NK
                        </div>
                        <span class="font-bold text-xl text-gray-800 tracking-tight">
                            NKBeauty<span class="text-pink-500">Salon</span>
                        </span>
                    </a>
                </div>

                <!-- MENU -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">

                    @auth
                        @if(auth()->user()->role === 'admin')

                            <!-- ADMIN MENU -->
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('admin.treatments.index')" :active="request()->routeIs('admin.treatments.*')">
                                Layanan
                            </x-nav-link>

                            <x-nav-link :href="route('admin.customers.index')">
                                Pelanggan
                            </x-nav-link>

                            <x-nav-link :href="route('admin.inventory.index')">
                                Stok
                            </x-nav-link>

                            <x-nav-link :href="route('admin.pos.create')">
                                Kasir
                            </x-nav-link>

                        @else

                            <!-- USER MENU -->
<x-nav-link :href="route('dashboard')">
    Beranda
</x-nav-link>

<x-nav-link :href="route('user.bookings')">
    Riwayat Reservasi Saya
</x-nav-link>

                        @endif
                    @endauth

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">

                <!-- 🔥 KERANJANG (FIX TOTAL) -->
                @auth
                    @if(auth()->user()->role === 'user')
                        <a href="{{ route('cart.index') }}"
                           class="relative text-gray-500 hover:text-pink-600 transition group flex items-center gap-1 mr-6 p-2 rounded-full hover:bg-pink-50">

                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5"/>
                                </svg>

                                {{-- 🔥 SAFE COUNT --}}
                                @php
                                    $cartCount = auth()->user()->carts->count() ?? 0;
                                @endphp

                                @if($cartCount > 0)
                                    <span class="absolute -top-1 -right-1 bg-pink-500 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endif
                @endauth

                <!-- DROPDOWN -->
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 rounded-full text-gray-600 hover:text-pink-600 hover:bg-pink-50">

                            <div class="w-7 h-7 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 text-xs font-bold mr-2">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>

                            <div>{{ Auth::user()->name }}</div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-400 border-b bg-gray-50">
                            Login sebagai:
                            <span class="font-bold text-pink-500">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

<form method="POST" action="{{ route('logout') }}" id="logout-form">
    @csrf
</form>

<a href="#"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
   class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50">
    Logout
</a>
                    </x-slot>
                </x-dropdown>
                @endauth

            </div>
        </div>
    </div>
</nav>