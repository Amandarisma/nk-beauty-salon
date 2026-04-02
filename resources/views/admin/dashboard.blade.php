<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-pink-600 leading-tight">
                {{ __('Dashboard Admin') }}
            </h2>
            <!-- Tombol Cepat ke Kasir -->
            <a href="{{ route('admin.pos.create') }}" class="bg-pink-600 text-white px-6 py-2.5 rounded-full font-bold hover:bg-pink-700 shadow-lg flex items-center gap-2 transition transform hover:-translate-y-1 hover:shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Walk-in (Kasir)
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- WIDGET RINGKASAN (Full Warna Pastel) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card 1: Reservasi Baru (Pink Pastel) -->
                <div class="bg-pink-50 p-6 rounded-3xl shadow-sm border border-pink-100 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="p-4 bg-white rounded-2xl text-pink-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-pink-400 text-xs font-bold uppercase tracking-wider">Menunggu Layanan</p>
                        <p class="text-3xl font-extrabold text-pink-700">{{ $pendingBookings ?? 0 }}</p>
                    </div>
                </div>

                <!-- Card 2: Total Transaksi (Ungu Pastel) -->
                <div class="bg-purple-50 p-6 rounded-3xl shadow-sm border border-purple-100 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="p-4 bg-white rounded-2xl text-purple-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Total Booking</p>
                        <p class="text-3xl font-extrabold text-purple-700">{{ $totalBookings ?? 0 }}</p>
                    </div>
                </div>

                <!-- Card 3: Total Pelanggan (Biru Pastel) -->
                <div class="bg-blue-50 p-6 rounded-3xl shadow-sm border border-blue-100 flex items-center space-x-4 hover:shadow-md transition">
                    <div class="p-4 bg-white rounded-2xl text-blue-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Pelanggan</p>
                        <p class="text-3xl font-extrabold text-blue-700">{{ $totalCustomers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- KALENDER JADWAL -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-8 bg-white">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <span class="text-2xl">📅</span> Jadwal Booking
                        </h3>
                        <div class="text-sm font-medium flex gap-4 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                            <span class="flex items-center gap-2 text-gray-600"><div class="w-3 h-3 rounded-full bg-yellow-400 ring-2 ring-yellow-200"></div> Menunggu</span>
                            <span class="flex items-center gap-2 text-gray-600"><div class="w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-emerald-200"></div> Lunas/DP OK</span>
                        </div>
                    </div>
                    
                    <!-- Area Kalender -->
                    <div id="calendar" class="fc-theme-standard"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- STYLE KHUSUS KALENDER (Lebih Rapi & Jelas) -->
    <style>
        /* Toolbar Pastel */
        .fc-toolbar-title { font-size: 1.5rem !important; color: #db2777; font-weight: 800; letter-spacing: -0.5px; }
        .fc-button-primary { background-color: #fff !important; color: #555 !important; border: 1px solid #f3f4f6 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; border-radius: 8px !important; transition: all 0.2s; }
        .fc-button-active { background-color: #fce7f3 !important; color: #db2777 !important; font-weight: bold; border-color: #fbcfe8 !important; }
        .fc-button-primary:hover { background-color: #f9fafb !important; border-color: #db2777 !important; color: #db2777 !important; }
        
        /* Grid & Header */
        .fc-daygrid-day-number { color: #6b7280; font-weight: 600; text-decoration: none !important; margin: 4px; }
        .fc-col-header-cell { background-color: #fff; border-bottom: 2px solid #fce7f3 !important; padding: 16px 0 !important; }
        .fc-col-header-cell-cushion { color: #db2777; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6 !important; }

        /* Highlight Hari Ini (Kuning Lebih Terang) */
        .fc-day-today { background-color: #fff7cd !important; } 

        /* Event Styling (Kotak Jadwal) - RAPID & TIDAK MELUBER */
        .fc-event {
            border: none !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            font-size: 0.8rem !important;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Margin agar tidak nempel garis */
            margin-bottom: 4px !important;
            margin-left: 2px !important; 
            margin-right: 2px !important;
            
            /* CSS ANTI MELUBER: Wajib ada agar teks panjang jadi titik-titik */
            white-space: nowrap !important; 
            overflow: hidden !important;
            text-overflow: ellipsis !important; 
            max-width: 98% !important;
            cursor: pointer;
        }
        .fc-event:hover { transform: scale(1.02); z-index: 50; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        /* Warna Event (Override default blue) */
        .fc-h-event { background-color: #10b981; border: none; }
        .fc-daygrid-event-dot { border-color: #10b981; }
    </style>

    <!-- SCRIPT -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                
                // 🔥 1. TAMBAH INI AGAR TAMPILAN JAM DI KOTAK KALENDER JADI 24 JAM
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'List Agenda' },
                events: @json($events ?? []), 
                dayMaxEvents: true, 
                
                eventClick: function(info) {
                    var props = info.event.extendedProps;
                    
                    Swal.fire({
                        title: 'Detail Booking',
                        html: `
                            <div class="text-left mt-2">
                                <div class="bg-pink-50 p-4 rounded-xl border border-pink-100 mb-4">
                                    <p class="text-xs text-pink-500 uppercase font-bold tracking-wider mb-1">Nama Pelanggan</p>
                                    <p class="text-xl font-bold text-gray-800">${info.event.title}</p>
                                    <p class="text-sm text-gray-500 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        ${info.event.start.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit', hour12: false})} WIB
                                    </p>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <p class="font-bold text-gray-700 border-b border-gray-100 pb-1 text-sm">Layanan yang diambil:</p>
                                    <div class="text-gray-600 pl-2 text-sm leading-relaxed">${props.treatments}</div>
                                </div>

                                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm">
                                    <div class="flex justify-between font-bold text-lg text-pink-600">
                                        <span>Sisa Pelunasan:</span>
                                        <span>Rp ${props.sisa_bayar}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 text-right italic">Total Asli: Rp ${props.total_asli} | Sudah DP: Rp ${props.sudah_bayar}</p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false, 
                        showCloseButton: true,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>