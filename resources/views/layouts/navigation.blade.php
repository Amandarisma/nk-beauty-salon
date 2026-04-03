<nav class="bg-white border-b border-pink-100 sticky top-0 z-[9999] shadow-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex items-center">

                <!-- LOGO -->
                <a href="{{ route('home') }}"
                   class="flex items-center gap-2 hover:scale-105 transition">

                    <div class="w-9 h-9 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                        NK
                    </div>

                    <span class="font-bold text-xl">
                        NKBeauty<span class="text-pink-500">Salon</span>
                    </span>
                </a>

                <!-- MENU -->
                <div class="hidden sm:flex space-x-8 ml-10">

                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Beranda
                    </x-nav-link>

                    <x-nav-link :href="route('user.bookings')" :active="request()->routeIs('user.bookings')">
                        Riwayat Reservasi Saya
                    </x-nav-link>

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex items-center ml-6">

                @auth

                    <!-- CART -->
                    <a href="{{ route('cart.index') }}" class="relative mr-6">
                        🛒
                        @php $count = auth()->user()->carts()->count(); @endphp

                        @if($count > 0)
                            <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs px-1 rounded-full">
                                {{ $count }}
                            </span>
                        @endif
                    </a>

                    <!-- DROPDOWN -->
                    <div class="relative" x-data="{ open: false }">

                        <!-- BUTTON -->
                        <button type="button"
                                @click.stop="open = !open"
                                class="flex items-center gap-2 text-gray-700 hover:text-pink-600">

                            <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center font-bold text-pink-600">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </div>

                            Halo, {{ Auth::user()->name }}
                        </button>

                        <!-- MENU -->
                        <div x-show="open"
                             x-transition
                             x-cloak
                             @click.outside="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-[9999]">

                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm hover:bg-gray-100">
                                Setting Profile
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>

                        </div>

                    </div>

                @endauth

            </div>
        </div>
    </div>
</nav>