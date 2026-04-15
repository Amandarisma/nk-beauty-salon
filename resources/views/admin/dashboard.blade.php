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

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-2xl mb-4 flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div onclick="showTodayList('next')" class="bg-pink-50 p-5 rounded-2xl shadow-sm border border-pink-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:scale-[1.01] transition transform">
                    <div class="p-3 bg-white rounded-xl text-pink-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-pink-400 text-xs font-bold uppercase tracking-wider">Antrean Selanjutnya</p>
                        <p class="text-2xl font-extrabold text-pink-700">{{ $antreanSelanjutnya ?? 0 }}</p>
                    </div>
                </div>

                <div onclick="showTodayList('all')" class="bg-purple-50 p-5 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:scale-[1.01] transition transform">
                    <div class="p-3 bg-white rounded-xl text-purple-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Total Booking</p>
                        <p class="text-2xl font-extrabold text-purple-700">{{ $totalBookingsToday ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-6">
                <div class="p-5 bg-white">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="text-xl">📅</span> Jadwal Kalender
                        </h3>
                        <div class="text-[10px] font-bold flex flex-wrap justify-end gap-3 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100 uppercase tracking-tighter">
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-yellow-100 border border-yellow-400"></div> Pending</span>
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-blue-100 border border-blue-400"></div> DP 30%</span>
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-emerald-100 border border-emerald-400"></div> Lunas</span>
                            <span class="flex items-center gap-1.5 text-gray-600"><div class="w-2.5 h-2.5 rounded-full bg-gray-100 border border-gray-400"></div> Selesai</span>
                        </div>
                    </div>
                    <div id="calendar" class="fc-theme-standard"></div>
                </div>
            </div>
        </div>
    </div>

    <form id="status-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="status-input">
    </form>

    <style>
        .fc-toolbar-title { font-size: 1.1rem !important; color: #db2777; font-weight: 800; }
        .fc-button-primary { background-color: #fff !important; color: #555 !important; border: 1px solid #f3f4f6 !important; border-radius: 6px !important; transition: all 0.2s; padding: 4px 10px !important; font-size: 0.8rem !important; font-weight: bold !important;}
        .fc-button-active { background-color: #fce7f3 !important; color: #db2777 !important; border-color: #fbcfe8 !important; }
        .fc-button-primary:hover { background-color: #f9fafb !important; color: #db2777 !important; }
        .fc-daygrid-day-number { color: #6b7280; font-weight: 600; text-decoration: none !important; margin: 2px; font-size: 0.8rem; }
        .fc-col-header-cell { background-color: #fff; border-bottom: 2px solid #fce7f3 !important; padding: 6px 0 !important; }
        .fc-col-header-cell-cushion { color: #db2777; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6 !important; }
        .fc-day-today { background-color: #fdf2f8 !important; } 
        .fc-event { border-width: 1px !important; padding: 2px 4px !important; border-radius: 4px !important; font-size: 0.7rem !important; font-weight: 700; margin-bottom: 2px !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; cursor: pointer; }
        .fc-daygrid-more-link { color: #db2777 !important; font-weight: bold !important; font-size: 0.7rem !important; padding-left: 4px; }
        .fc-list { border: none !important; }
        .fc-list-day-cushion { background-color: #fdf2f8 !important; color: #db2777 !important; font-weight: 800 !important; padding: 8px 12px !important; }
        .fc-list-event td { padding: 10px !important; border-bottom: 1px solid #f9fafb !important; cursor: pointer; }
        .fc-h-event { border: none !important; }
        .fc-event-main { font-weight: 700 !important; }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            // Menggunakan json_encode agar format JSON 100% aman dan tidak crash
            var eventsData = {!! json_encode($events ?? []) !!};
            var serverToday = '{{ \Carbon\Carbon::today()->format("Y-m-d") }}';

            var calendar = new FullCalendar.Calendar(calendarEl, {
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

            window.showDetail = function(event) {
                var props = event.extendedProps;
                
                var namaLayanan = props.layanan || 'Layanan Tercatat';
                var totalAsliText = props.total_asli || '0';
                var sudahDpText = props.sudah_dp || '0';
                var sisaText = props.sisa || '0';
                var sisaRaw = parseFloat(props.sisa_raw) || 0;

                var statusBadge = '';
                if (props.booking_status === 'completed' || props.booking_status === 'cancelled') {
                    statusBadge = '<span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wide uppercase">' + props.booking_status + '</span>';
                } else if (props.payment_status === 'paid') {
                    statusBadge = '<span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wide">LUNAS</span>';
                } else if (props.payment_status === 'paid_dp' || props.payment_status === 'dp') {
                    statusBadge = '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wide">DP 30%</span>';
                } else {
                    statusBadge = '<span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wide">BELUM BAYAR</span>';
                }

                var textSisaTagihan = sisaRaw <= 0 ? 'Rp 0 (LUNAS)' : 'Rp ' + sisaText;
                var warnaSisa = sisaRaw <= 0 ? 'text-emerald-500' : 'text-pink-600';

                var actionButtons = '';
                if (props.is_waiting) {
                    actionButtons = `
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100 justify-center">
                            <button onclick="processAction(${event.id}, 'completed', 'Selesaikan layanan ini?')" class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl hover:bg-emerald-500 hover:text-white transition text-sm font-bold w-full">
                                Selesai
                            </button>
                            <button onclick="processAction(${event.id}, 'cancelled', 'Batalkan booking ini? Slot di kalender akan kembali kosong.')" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl hover:bg-red-500 hover:text-white transition text-sm font-bold w-full">
                                Batalkan
                            </button>
                        </div>
                    `;
                }

                Swal.fire({
                    title: 'Detail Booking',
                    html: `
                        <div class="text-left mt-2">
                            <div class="bg-pink-50 p-4 rounded-2xl border border-pink-100 mb-4 shadow-inner">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs text-pink-500 uppercase font-bold tracking-wider">Pelanggan</p>
                                    ${statusBadge}
                                </div>
                                <p class="text-xl font-bold text-gray-800">${event.title}</p>
                                <p class="text-sm text-gray-500 mt-1">${props.waktu_lengkap || 'Waktu tidak tersedia'}</p>
                            </div>
                            
                            <div class="mb-4 px-2">
                                <p class="font-bold text-gray-700 border-b border-gray-200 pb-1 text-xs uppercase mb-2">Layanan yang dipilih:</p>
                                <div class="text-gray-600 text-sm leading-relaxed font-medium">${namaLayanan}</div>
                            </div>

                            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm mb-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2 border-b border-gray-100 pb-2">Rincian Pembayaran</p>
                                <div class="flex justify-between text-xs text-gray-600 mb-1 font-medium">
                                    <span>Total Biaya Layanan:</span>
                                    <span>Rp ${totalAsliText}</span>
                                </div>
                                <div class="flex justify-between text-xs text-emerald-600 font-bold mb-2 border-b border-gray-100 pb-2">
                                    <span>Sudah Dibayar:</span>
                                    <span>- Rp ${sudahDpText}</span>
                                </div>
                                <div class="flex justify-between font-black text-lg ${warnaSisa}">
                                    <span>Sisa Tagihan:</span>
                                    <span>${textSisaTagihan}</span>
                                </div>
                            </div>

                            ${actionButtons}
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'rounded-3xl shadow-2xl' }
                });
            };

            window.showTodayList = function(type) {
                var now = new Date();
                var currentHour = String(now.getHours()).padStart(2, '0');
                var currentMinute = String(now.getMinutes()).padStart(2, '0');
                var currentTime = currentHour + ':' + currentMinute;

                var filtered = eventsData.filter(ev => {
                    if (!ev.start) return false;
                    var evDate = ev.start.split('T')[0];
                    if (evDate !== serverToday) return false; 
                    
                    if (type === 'next') {
                        var evTime = '00:00';
                        if (ev.start.includes('T')) {
                            evTime = ev.start.split('T')[1].substring(0, 5);
                        }
                        return ev.extendedProps.is_waiting === true && evTime >= currentTime;
                    }
                    return true;
                });

                if (filtered.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Kosong',
                        text: 'Tidak ada data ' + (type === 'next' ? 'antrean selanjutnya' : 'booking') + ' untuk hari ini.',
                        customClass: { popup: 'rounded-2xl' }
                    });
                    return;
                }

                var listHtml = '<div class="text-left space-y-3 max-h-80 overflow-y-auto pr-2 mt-4">';
                filtered.forEach(ev => {
                    var jamWib = '';
                    if (ev.start && ev.start.includes('T')) {
                        jamWib = ev.start.split('T')[1].substring(0,5);
                    }

                    listHtml += `
                        <div class="p-4 border border-gray-100 rounded-2xl flex justify-between items-center bg-gray-50 hover:bg-white hover:shadow-sm transition">
                            <div>
                                <p class="font-bold text-gray-800">${ev.title}</p>
                                <p class="text-[10px] font-bold text-pink-500 uppercase">${jamWib} WIB</p>
                            </div>`;
                    
                    if (type === 'next') {
                        listHtml += `
                            <div class="flex gap-2">
                                <button onclick="processAction(${ev.id}, 'completed', 'Selesaikan layanan ini?')" class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-xl hover:bg-emerald-500 hover:text-white transition text-xs font-bold">
                                    Selesai
                                </button>
                                <button onclick="processAction(${ev.id}, 'cancelled', 'Batalkan booking ini?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-xl hover:bg-red-500 hover:text-white transition text-xs font-bold">
                                    Batalkan
                                </button>
                            </div>`;
                    }
                    listHtml += '</div>';
                });
                listHtml += '</div>';

                Swal.fire({
                    title: type === 'next' ? 'Antrean Selanjutnya' : 'Semua Agenda Hari Ini',
                    html: listHtml,
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'rounded-3xl shadow-2xl' }
                });
            };

            window.processAction = function(id, status, message) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: status === 'cancelled' ? '#ef4444' : '#10b981',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Tutup',
                    customClass: { popup: 'rounded-3xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = document.getElementById('status-form');
                        form.action = "{{ url('admin/bookings') }}/" + id + "/status";
                        document.getElementById('status-input').value = status;
                        form.submit();
                    }
                });
            };
        });
    </script>
</x-app-layout>