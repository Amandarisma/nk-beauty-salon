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
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                {{-- TANGGAL --}}
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Tanggal</label>
                    <input 
                        type="date" 
                        id="booking_date" 
                        class="w-full border rounded px-3 py-2"
                        required
                    >
                </div>

                {{-- JAM --}}
                <div class="mb-4">
                    <label class="block font-semibold mb-1">Jam</label>
                    <select 
                        id="booking_time" 
                        class="w-full border rounded px-3 py-2"
                        required
                    >
                        <option value="">-- pilih jam --</option>
                    </select>
                </div>

                <button class="w-full bg-pink-500 text-white py-2 rounded-lg font-bold">
                    Checkout
                </button>
            </form>

            <script>
const dateInput = document.getElementById('booking_date');
const timeSelect = document.getElementById('booking_time');

let userDuration = 60; // default

// 🔥 ambil durasi dari cart
fetch('/cart/total-duration/data')
    .then(res => res.json())
    .then(data => {
        userDuration = data.duration || 60;
    });

// 🔥 generate slot per 30 menit
function generateSlots() {
    let slots = [];
    let start = 9 * 60;   // 09:00
    let end = 17 * 60;    // 17:00

    for (let t = start; t < end; t += 30) {
        let h = Math.floor(t / 60);
        let m = t % 60;
        slots.push(`${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`);
    }

    return slots;
}

// 🔥 cek apakah bentrok
function isConflict(time, bookedSlots) {
    let [h, m] = time.split(':').map(Number);
    let start = h * 60 + m;
    let end = start + userDuration;

    for (let t = start; t < end; t += 30) {
        let hh = Math.floor(t / 60);
        let mm = t % 60;
        let check = `${String(hh).padStart(2,'0')}:${String(mm).padStart(2,'0')}`;

        if (bookedSlots.includes(check)) {
            return true;
        }
    }

    return false;
}

// 🔥 load slot
dateInput.addEventListener('change', async function () {

    const date = this.value;

    const res = await fetch(`/api/booked-slots?date=${date}`);
    const bookedSlots = await res.json();

    timeSelect.innerHTML = '<option value="">-- pilih jam --</option>';

    const slots = generateSlots();

    slots.forEach(time => {

        let option = document.createElement('option');
        option.value = time;

        // 🔥 langsung cek tanpa function tambahan
        if (bookedSlots.includes(time)) {
            option.textContent = time + " 🔴 FULL";
            option.disabled = true;
        } else {
            option.textContent = time;
        }

        timeSelect.appendChild(option);
    });

});

// 🔥 update cart realtime
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
    });
});


</script>

        </div>
    </div>

<script>
const dateInput = document.getElementById('booking_date');
const timeSelect = document.getElementById('booking_time');

// 🔥 GENERATE SLOT DINAMIS (SMART SLOT)
function generateTimeSlots(start = "09:00", end = "17:00", interval = 60) {
    let slots = [];
    let [h, m] = start.split(':').map(Number);

    while (true) {
        let time = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
        slots.push(time);

        m += interval;
        if (m >= 60) {
            h += Math.floor(m / 60);
            m = m % 60;
        }

        if (`${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}` >= end) break;
    }

    return slots;
}

// 🔥 LOAD SLOT
dateInput.addEventListener('change', function () {
    const date = this.value;

    fetch(`/api/booked-slots?date=${date}`)
        .then(res => res.json())
        .then(bookedSlots => {

            timeSelect.innerHTML = '<option value="">-- pilih jam --</option>';

            const allSlots = generateTimeSlots();

            allSlots.forEach(time => {

                let option = document.createElement('option');
                option.value = time;

                if (bookedSlots.includes(time)) {
                    option.textContent = time + " (TERISI)";
                    option.disabled = true;
                } else {
                    option.textContent = time;
                }

                timeSelect.appendChild(option);
            });

        });
});

// 🔥 REALTIME UPDATE CART
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
    })
    .then(res => res.json())
    .then(data => {
        console.log("Cart updated:", data);
    });

});
</script>
</x-app-layout>