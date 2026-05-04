<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-pink-600">
            Booking Jadwal
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow">

            {{-- ALERT --}}
            @if(session('error'))
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 font-bold">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                {{-- TANGGAL --}}
                <div class="mb-4">
                    <label class="block font-semibold mb-1 text-gray-700">Pilih Tanggal</label>
                    <input 
                        type="date" 
                        id="booking_date" 
                        name="booking_date"
                        class="w-full border-gray-300 rounded-xl px-4 py-2 focus:ring-pink-500 focus:border-pink-500"
                        required
                    >
                </div>

                {{-- JAM --}}
                <div class="mb-6">
                    <label class="block font-semibold mb-1 text-gray-700">Pilih Jam</label>
                    <select 
                        id="booking_time" 
                        name="booking_time"
                        class="w-full border-gray-300 rounded-xl px-4 py-2 focus:ring-pink-500 focus:border-pink-500"
                        required
                    >
                        <option value="">-- Pilih Jam (10:00 - 17:00) --</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-xl font-bold transition duration-200">
                    Simpan Keranjang & Lanjut
                </button>
            </form>

        </div>
    </div>

{{-- 🔥 SCRIPT MATEMATIKA WAKTU (ANTI-JEBOL) 🔥 --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking_date');
    const timeSelect = document.getElementById('booking_time');

    let userDuration = 60; // Default

    // Ambil durasi
    fetch('/cart/total-duration/data')
        .then(res => res.json())
        .then(data => {
            if (data.duration) userDuration = data.duration;
        })
        .catch(err => console.log("Gagal ambil durasi:", err));

    // Bikin opsi jam
    function generateSlots() {
        let slots = [];
        for (let t = 10 * 60; t <= 17 * 60; t += 30) {
            let h = Math.floor(t / 60);
            let m = t % 60;
            slots.push(`${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`);
        }
        return slots;
    }

    // Saat tanggal diklik
    dateInput.addEventListener('change', async function () {
        const selectedDate = this.value; // Dapatnya format YYYY-MM-DD
        if (!selectedDate) return;

        timeSelect.innerHTML = '<option value="">Sedang memuat jadwal...</option>';

        try {
            const res = await fetch(`/api/booked-slots?date=${selectedDate}`);
            const bookedSlots = await res.json();

            timeSelect.innerHTML = '<option value="">-- Pilih Jam (10:00 - 17:00) --</option>';
            const slots = generateSlots();

            // 🔥 LOGIKA MATEMATIKA WAKTU 🔥
            const now = new Date(); // Detik ini juga
            
            // Pecah tanggal yang dipilih jadi Angka (Tahun, Bulan, Hari)
            const [year, month, day] = selectedDate.split('-').map(Number);

            slots.forEach(time => {
                let option = document.createElement('option');
                option.value = time;

                // Pecah jam di list jadi Angka (Jam, Menit)
                const [h, m] = time.split(':').map(Number);
                
                // Rakit jadi Objek Waktu yang SUPER AKURAT
                // Catatan: Di JS bulan dimulai dari 0, makanya month - 1
                const slotDateTime = new Date(year, month - 1, day, h, m, 0);

                // CEK MUTLAK: Apakah waktu slot tersebut LEBIH KECIL (sudah lewat) dari waktu sekarang?
                const isPastTime = slotDateTime < now;

                if (bookedSlots.includes(time) || bookedSlots.includes(time+':00') || isPastTime) {
                    
                    if (isPastTime && !bookedSlots.includes(time) && !bookedSlots.includes(time+':00')) {
                        option.textContent = time + " WIB (Sudah Lewat)";
                    } else {
                        option.textContent = time + " WIB (Sudah Dipesan)";
                    }
                    
                    option.disabled = true; // Langsung dimatikan!
                    option.classList.add('bg-gray-100', 'text-gray-400');
                    
                } else {
                    option.textContent = time + " WIB";
                }

                timeSelect.appendChild(option);
            });

        } catch (error) {
            console.error("Gagal load jadwal:", error);
            timeSelect.innerHTML = '<option value="">Gagal memuat jadwal. Coba lagi.</option>';
        }
    });

    // Update Keranjang
    timeSelect.addEventListener('change', function () {
        const date = dateInput.value;
        const time = this.value;

        if (!date || !time) return;

        fetch("{{ route('cart.updateSchedule') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                booking_date: date,
                booking_time: time
            })
        }).catch(err => console.log("Gagal update cart:", err));
    });
});
</script>
</x-app-layout>