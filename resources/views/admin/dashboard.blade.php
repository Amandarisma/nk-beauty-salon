<x-app-layout>
    <div class="py-4 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- HEADER -->
            <div class="flex justify-between items-center mb-5 mt-2">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">Ringkasan Hari Ini</h2>
                    <p class="text-sm text-gray-500">Aktivitas salon tanggal {{ \Carbon\Carbon::today('Asia/Jakarta')->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('admin.pos.create') }}" class="bg-pink-600 text-white px-5 py-2.5 rounded-full font-bold hover:bg-pink-700 shadow-md flex items-center gap-2 transition transform hover:-translate-y-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Kasir (Walk-in)
                </a>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-2xl mb-4 flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- CARD RINGKASAN HARI INI -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                
                <!-- KARTU ANTREAN -->
                <div onclick="showTodayList('next')" class="group bg-pink-50 p-5 rounded-2xl shadow-sm border border-pink-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:bg-pink-100 transition transform relative overflow-hidden">
                    <div class="p-3 bg-white rounded-xl text-pink-500 shadow-sm group-hover:bg-pink-500 group-hover:text-white transition z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="z-10 flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-pink-400 text-xs font-bold uppercase tracking-wider">Antrean Selanjutnya</p>
                                @if($antreanSelanjutnyaWaktu !== '0')
                                    <p class="text-2xl font-extrabold text-pink-700">{{ $antreanSelanjutnyaWaktu }}</p>
                                @else
                                    <p class="text-2xl font-extrabold text-pink-700">0</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($antreanSelanjutnyaWaktu !== '0')
                            <div class="mt-1 pt-1 border-t border-pink-200/50">
                                <p class="text-[11px] font-bold text-gray-700 truncate"><span class="text-pink-600">👤</span> {{ $antreanSelanjutnyaPelanggan }}</p>
                                <p class="text-[10px] font-bold text-pink-500 truncate mt-0.5"><span class="text-gray-400">✂️</span> {{ $antreanSelanjutnyaLayanan }}</p>
                            </div>
                        @else
                            <p class="text-xs font-medium text-pink-500/70 mt-1">Belum ada antrean masuk</p>
                        @endif
                    </div>
                </div>

                <!-- KARTU TOTAL BOOKING -->
                <div onclick="showTodayList('all')" class="group bg-purple-50 p-5 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4 cursor-pointer hover:shadow-md hover:bg-purple-100 transition transform">
                    <div class="p-3 bg-white rounded-xl text-purple-500 shadow-sm group-hover:bg-purple-500 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Total Booking Hari Ini</p>
                        <p class="text-3xl font-extrabold text-purple-700">{{ $totalBookingsToday }}</p>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- SECTION BARU: LAPORAN OPERASIONAL -->
            <!-- ============================================== -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="text-xl">📊</span> Laporan Operasional
                    </h3>
                    
                    <!-- NAVIGASI BULAN GLOBAL -->
                    <div class="flex items-center gap-2">
                        <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <a href="{{ route('admin.dashboard', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="px-3 py-1.5 hover:bg-gray-50 border-r border-gray-200 flex items-center justify-center transition">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                            <span class="px-4 py-1.5 text-xs font-bold text-gray-700 w-32 text-center bg-gray-50 uppercase tracking-widest">
                                {{ $currentDate->translatedFormat('F Y') }}
                            </span>
                            <a href="{{ route('admin.dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="px-3 py-1.5 hover:bg-gray-50 border-l border-gray-200 flex items-center justify-center transition">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-1.5 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-600 font-medium hover:bg-gray-50 hover:text-pink-600 text-sm transition">
                            Bulan Ini
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- GRAFIK KEUANGAN -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800">Grafik Keuangan per Bulan</h4>
                                <!-- 🔥 TEKS BULAN OTOMATIS BERUBAH 🔥 -->
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">TOTAL PEMASUKAN {{ strtoupper($currentDate->translatedFormat('F Y')) }}</p>
                                <p class="text-3xl font-black text-pink-600 mt-1 tracking-tight">
                                    Rp {{ number_format($totalKeuanganBulanIni, 0, ',', '.') }}
                                </p>
                            </div>
                            <button type="button" onclick="document.getElementById('modalExport').classList.remove('hidden')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-xl text-xs font-bold border border-red-100 hover:bg-red-500 hover:text-white transition flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                PDF
                            </button>
                        </div>
                        <div class="flex-1 min-h-[300px] relative w-full mt-4">
                            <canvas id="revenueLineChart"></canvas>
                        </div>
                    </div>

                    <!-- LAYANAN POPULER & TOP 5 -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-4">
                            <!-- 🔥 TEKS BULAN OTOMATIS BERUBAH 🔥 -->
                            <h4 class="font-bold text-gray-800">Layanan Populer ({{ $currentDate->translatedFormat('F Y') }})</h4>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">BERDASARKAN TOTAL RESERVASI SELESAI</p>
                            <p class="text-sm font-bold text-gray-600 mt-2 border-b border-gray-50 pb-2">Total Item Dipesan: <span class="text-pink-600 font-black">{{ $totalItemTerjual }}</span> Item</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-6 mt-2 flex-1">
                            <div class="flex flex-col items-center w-full sm:w-1/2">
                                <div class="relative h-48 w-48 shrink-0 mb-4">
                                    <canvas id="popularPieChart"></canvas>
                                </div>
                            </div>
                            
                            <div class="w-full sm:w-1/2 border-t sm:border-t-0 sm:border-l border-gray-50 pt-4 sm:pt-0 sm:pl-6 h-full flex flex-col justify-center">
                                <h5 class="text-[11px] text-pink-500 font-bold uppercase tracking-widest mb-4">Top 5 Populer</h5>
                                <ul class="space-y-4">
                                    @forelse($top5Layanan as $index => $item)
                                        <li class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-gray-300 text-xs w-3">{{ $index + 1 }}.</span>
                                                <span class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $pieColors[$index] }}"></span>
                                                <span class="font-bold text-gray-700 text-xs">{{ $item->name }}</span>
                                            </div>
                                            <div class="font-black text-gray-800 text-[11px] bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                                {{ $item->total_dipesan }}x
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-xs text-gray-400 py-2">Belum ada data reservasi.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KALENDER EXISTING -->
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

    <!-- MODAL EXPORT PDF DINAMIS (Bulan Ini / Rentang Bulan) -->
    <div id="modalExport" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl mx-4 transform transition-all">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-100 text-red-500 rounded-full flex items-center justify-center shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800">Export Laporan PDF</h3>
            </div>
            
            <form action="{{ route('admin.export.pdf') }}" method="GET">
                <div class="space-y-5 mb-8">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Mode Export</label>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center p-3 border border-pink-200 rounded-xl cursor-pointer hover:bg-pink-50 transition">
                            <input type="radio" name="export_mode" value="current" class="text-pink-600 focus:ring-pink-500" checked onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Bulan Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('F Y') }})</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="export_mode" value="specific" class="text-pink-600 focus:ring-pink-500" onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Bulan Tertentu</span>
                        </label>

                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="export_mode" value="range" class="text-pink-600 focus:ring-pink-500" onchange="toggleExportInputs()">
                            <span class="ml-3 text-sm font-bold text-gray-700">Rentang Bulan</span>
                        </label>
                    </div>

                    <!-- Input Bulan Tertentu -->
                    <div id="input-specific" class="hidden animate-fade-in">
                        <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2 mt-2">Pilih Bulan</label>
                        <input type="month" name="specific_month" class="w-full border-gray-200 rounded-xl px-4 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                    </div>

                    <!-- Input Rentang Bulan -->
                    <div id="input-range" class="hidden grid-cols-2 gap-3 animate-fade-in mt-2">
                        <div>
                            <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2">Dari Bulan</label>
                            <input type="month" name="start_month" class="w-full border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-pink-500 uppercase mb-2">Sampai Bulan</label>
                            <input type="month" name="end_month" class="w-full border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-700 bg-gray-50 focus:ring-pink-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modalExport').classList.add('hidden')" class="flex-1 py-3 bg-gray-100 rounded-xl font-bold text-gray-500 hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-pink-600 text-white rounded-xl font-bold shadow-lg shadow-pink-200 hover:bg-pink-700 transition">Download</button>
                </div>
            </form>
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
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Logika untuk menampilkan input sesuai mode Export yang dipilih
        function toggleExportInputs() {
            const mode = document.querySelector('input[name="export_mode"]:checked').value;
            const specificInput = document.getElementById('input-specific');
            const rangeInput = document.getElementById('input-range');

            specificInput.classList.add('hidden');
            rangeInput.classList.remove('grid');
            rangeInput.classList.add('hidden');

            if (mode === 'specific') {
                specificInput.classList.remove('hidden');
            } else if (mode === 'range') {
                rangeInput.classList.remove('hidden');
                rangeInput.classList.add('grid');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            Chart.register(ChartDataLabels);

            // --- 1. LOGIKA GRAFIK KEUANGAN ---
            const ctxLine = document.getElementById('revenueLineChart');
            if(ctxLine) {
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: @json($revenueLabelsX),
                        datasets: @json($chartDataFinance)
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        layout: {
                            padding: { top: 15, right: 20, bottom: 10 }
                        },
                        plugins: { 
                            legend: { display: true, position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: {size: 11, weight: 'bold'} } },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#db2777',
                                bodyColor: '#4b5563',
                                borderColor: '#fce7f3',
                                borderWidth: 1,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12, weight: 'bold' },
                                padding: 12,
                                boxPadding: 6,
                                callbacks: {
                                    label: function(context) {
                                        if (context.raw > 0) {
                                            return ' ' + context.dataset.label + ' : Rp ' + context.raw.toLocaleString('id-ID');
                                        }
                                        return null; 
                                    }
                                }
                            },
                            datalabels: { display: false }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grace: '20%',
                                title: { display: true, text: 'Pendapatan (Rp)', font: {weight: 'bold', size: 10}, color: '#9ca3af' },
                                grid: { color: '#f8fafc', drawBorder: false, borderDash: [5, 5] },
                                ticks: {
                                    color: '#6b7280',
                                    font: {size: 10},
                                    callback: function(value) {
                                        if(value >= 1000000) return 'Rp ' + (value/1000000) + 'Jt';
                                        if(value >= 1000) return 'Rp ' + (value/1000) + 'K';
                                        return 'Rp ' + value;
                                    }
                                }
                            },
                            x: { 
                                title: { display: true, text: 'Tanggal', font: {weight: 'bold', size: 10}, color: '#9ca3af' },
                                grid: { display: false, drawBorder: false },
                                ticks: { maxRotation: 45, minRotation: 45, color: '#6b7280', font: {size: 10} }
                            }
                        }
                    }
                });
            }

            // --- 2. LOGIKA LAYANAN POPULER ---
            const ctxPop = document.getElementById('popularPieChart');
            const dataLayanan = @json($pieValues ?? []);
            const persentaseLayanan = @json($persentaseLayanan ?? []);

            if(ctxPop) {
                if(dataLayanan.length > 0) {
                    new Chart(ctxPop, {
                        type: 'doughnut',
                        data: {
                            labels: @json($pieLabels ?? []),
                            datasets: [{
                                data: dataLayanan,
                                backgroundColor: @json($pieColors ?? []),
                                borderWidth: 0,
                                hoverOffset: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    titleColor: '#db2777',
                                    bodyColor: '#4b5563',
                                    borderColor: '#fce7f3',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.raw || 0;
                                            let percent = persentaseLayanan[context.dataIndex];
                                            return ` ${label}: ${value} Pesanan (${percent}%)`;
                                        }
                                    }
                                },
                                datalabels: { 
                                    color: '#ffffff',
                                    font: { weight: 'bold', size: 12 },
                                    formatter: function(value, context) {
                                        let percent = persentaseLayanan[context.dataIndex];
                                        return percent > 5 ? percent + '%' : ''; 
                                    }
                                }
                            }
                        }
                    });
                } else {
                    ctxPop.parentElement.innerHTML = '<div class="flex items-center justify-center h-full w-full"><span class="text-xs font-bold text-gray-400 bg-gray-50 border-2 border-dashed border-gray-200 rounded-full h-32 w-32 flex items-center justify-center">Data Kosong</span></div>';
                }
            }

            // --- 3. LOGIKA KALENDER ASLI ---
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            var eventsData = {!! json_encode($events ?? []) !!};
            var serverToday = '{{ \Carbon\Carbon::today("Asia/Jakarta")->format("Y-m-d") }}';

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
                            <button onclick="processAction(${event.id}, 'completed', 'Selesaikan layanan ini?')" class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl hover:bg-emerald-50 hover:text-white transition text-sm font-bold w-full">
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
                                <button onclick="processAction(${event.id}, 'cancelled', 'Batalkan booking ini?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-xl hover:bg-red-500 hover:text-white transition text-xs font-bold">
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