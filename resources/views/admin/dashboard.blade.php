<x-app-layout>
    <div class="py-4 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-5 mt-2">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">Ringkasan Hari Ini</h2>
                    <p class="text-sm text-gray-500">Aktivitas salon tanggal {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('admin.pos.create') }}" class="bg-pink-600 text-white px-5 py-2.5 rounded-full font-bold hover:bg-pink-700 shadow-md flex items-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Kasir (Walk-in)
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div onclick="showTodayList('pending')" class="bg-pink-50 p-5 rounded-2xl shadow-sm border border-pink-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:scale-[1.02] transition transform">
                    <div class="p-3 bg-white rounded-xl text-pink-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-pink-400 text-xs font-bold uppercase tracking-wider">Menunggu Hari Ini</p>
                        <p class="text-2xl font-extrabold text-pink-700">{{ $pendingBookingsToday ?? 0 }}</p>
                    </div>
                </div>

                <div onclick="showTodayList('all')" class="bg-purple-50 p-5 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:scale-[1.02] transition transform">
                    <div class="p-3 bg-white rounded-xl text-purple-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Total Booking Hari Ini</p>
                        <p class="text-2xl font-extrabold text-purple-700">{{ $totalBookingsToday ?? 0 }}</p>
                    </div>
                </div>

                <div class="bg-blue-50 p-5 rounded-2xl shadow-sm border border-blue-100 flex items-center space-x-4">
                    <div class="p-3 bg-white rounded-xl text-blue-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Total Member</p>
                        <p class="text-2xl font-extrabold text-blue-700">{{ $totalCustomers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-5 bg-white">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="text-xl">📅</span> Jadwal Kalender
                        </h3>
                        <div class="text-[10px] font-bold flex gap-3 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100 uppercase tracking-tighter">
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-yellow-100 border border-yellow-400"></div> Menunggu</span>
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-emerald-100 border border-emerald-400"></div> Lunas/DP OK</span>
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-gray-100 border border-gray-400"></div> Selesai</span>
                        </div>
                    </div>
                    
                    <div id="calendar" class="fc-theme-standard"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fc-toolbar-title { font-size: 1.1rem !important; color: #db2777; font-weight: 800; }
        .fc-button-primary { background-color: #fff !important; color: #555 !important; border: 1px solid #f3f4f6 !important; border-radius: 6px !important; transition: all 0.2s; padding: 4px 10px !important; font-size: 0.8rem !important; font-weight: bold !important;}
        .fc-button-active { background-color: #fce7f3 !important; color: #db2777 !important; border-color: #fbcfe8 !important; }
        .fc-button-primary:hover { background-color: #f9fafb !important; color: #db2777 !important; }
        
        .fc-daygrid-day-number { color: #6b7280; font-weight: 600; text-decoration: none !important; margin: 2px; font-size: 0.8rem; }
        .fc-col-header-cell { background-color: #fff; border-bottom: 2px solid #fce7f3 !important; padding: 6px 0 !important; }
        .fc-col-header-cell-cushion { color: #db2777; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6 !important; }

        /* 🔥 HIGHLIGHT HARI INI PINK SOFT */
        .fc-day-today { background-color: #fdf2f8 !important; } 

        .fc-event {
            border-width: 1px !important; padding: 2px 4px !important; border-radius: 4px !important;
            font-size: 0.7rem !important; font-weight: 700; margin-bottom: 2px !important;
            white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; cursor: pointer;
        }

        /* Styling '+ lainnya' */
        .fc-daygrid-more-link { color: #db2777 !important; font-weight: bold !important; font-size: 0.7rem !important; padding-left: 4px; }
        
        /* List Agenda Styling */
        .fc-list { border: none !important; }
        .fc-list-day-cushion { background-color: #fdf2f8 !important; color: #db2777 !important; font-weight: 800 !important; padding: 8px 12px !important; }
        .fc-list-event td { padding: 10px !important; border-bottom: 1px solid #f9fafb !important; cursor: pointer; }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eventsData = @json($events ?? []);
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                contentHeight: 480,
                aspectRatio: 2.2,
                eventDisplay: 'block',
                displayEventTime: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' },
                buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'Agenda' },
                events: eventsData, 
                dayMaxEvents: 2, 
                moreLinkText: 'lainnya',
                eventClick: function(info) {
                    showDetail(info.event);
                }
            });
            calendar.render();

            // 🔥 1. FUNGSI POPUP DETAIL (KLIK DI KALENDER)
            window.showDetail = function(event) {
                const props = event.extendedProps;
                Swal.fire({
                    title: 'Detail Booking',
                    html: `
                        <div class="text-left mt-2">
                            <div class="bg-pink-50 p-4 rounded-2xl border border-pink-100 mb-4 text-center">
                                <p class="text-xs text-pink-500 uppercase font-bold tracking-wider mb-1">Pelanggan</p>
                                <p class="text-xl font-bold text-gray-800">${event.title}</p>
                                <p class="text-sm text-gray-500 mt-1">${props.waktu_lengkap}</p>
                            </div>
                            <div class="mb-4">
                                <p class="font-bold text-gray-700 border-b pb-1 text-xs uppercase mb-2">Layanan:</p>
                                <div class="text-gray-600 text-sm leading-relaxed">${props.layanan}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <div class="flex justify-between font-bold text-lg text-pink-600">
                                    <span>Sisa Bayar:</span>
                                    <span>Rp ${props.sisa}</span>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-1 italic font-medium">
                                    <span>Total: Rp ${props.total_asli}</span>
                                    <span>DP: Rp ${props.sudah_dp}</span>
                                </div>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'rounded-3xl shadow-2xl' }
                });
            };

            // 🔥 2. FUNGSI KLIK CARD (LIST HARI INI)
            window.showTodayList = function(type) {
                const todayStr = new Date().toLocaleDateString('en-CA'); // Format YYYY-MM-DD
                const filtered = eventsData.filter(ev => {
                    const evDate = ev.start.split('T')[0];
                    if (evDate !== todayStr) return false;
                    // Logic: Kuning (#fef3c7) adalah status pending/menunggu
                    if (type === 'pending') return ev.backgroundColor === '#fef3c7';
                    return true;
                });

                if (filtered.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Kosong',
                        text: 'Tidak ada jadwal untuk kategori ini hari ini.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                    return;
                }

                let listHtml = `<div class="text-left space-y-3 max-h-80 overflow-y-auto pr-2">`;
                filtered.forEach(ev => {
                    listHtml += `
                        <div class="p-4 border border-gray-100 rounded-2xl flex justify-between items-center bg-gray-50 hover:bg-white hover:shadow-sm transition">
                            <div>
                                <p class="font-bold text-gray-800">${ev.title}</p>
                                <p class="text-[10px] font-bold text-pink-500 uppercase">${ev.start.split('T')[1].substring(0,5)} WIB</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="Swal.fire('Berhasil','Layanan diselesaikan!','success')" class="bg-emerald-100 text-emerald-600 p-2 rounded-xl hover:bg-emerald-500 hover:text-white transition" title="Selesai">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <button onclick="Swal.fire('Dibatalkan','Booking telah dihapus','error')" class="bg-red-100 text-red-600 p-2 rounded-xl hover:bg-red-500 hover:text-white transition" title="Batal/Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>`;
                });
                listHtml += `</div>`;

                Swal.fire({
                    title: type === 'pending' ? 'Antrean Menunggu' : 'Agenda Hari Ini',
                    html: listHtml,
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'rounded-3xl shadow-2xl' }
                });
            };
        });
    </script>
</x-app-layout>