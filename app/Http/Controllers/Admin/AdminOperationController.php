<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // 🔥 Ini posisi yang benar, di luar class!

class AdminOperationController extends Controller
{
    // ====================================================================
    // 1. DASHBOARD & GRAFIK
    // ====================================================================
    public function dashboard(Request $request)
    {
        $nowJakarta = Carbon::now('Asia/Jakarta');
        
        // FILTER WAKTU & NAVIGASI
        $selectedMonth = $request->input('month', $nowJakarta->month);
        $selectedYear = $request->input('year', $nowJakarta->year);
        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1, 'Asia/Jakarta');
        
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        // DATA KALENDER & RINGKASAN
        $today = Carbon::today('Asia/Jakarta');
        $todayDateStr = $nowJakarta->format('Y-m-d');
        $currentTimeStr = $nowJakarta->format('H:i:s');

        $totalBookingsToday = Booking::whereDate('booking_date', $today)->count();
        $pendingBookingsToday = Booking::whereDate('booking_date', $today)
            ->whereNotIn('booking_status', ['completed', 'cancelled'])->count();

        $bookings = Booking::with(['user', 'items.treatment'])->where('booking_status', '!=', 'cancelled')->get();
        $events = []; 

        foreach($bookings as $booking) {
            $firstItem = $booking->items->first();
            if($firstItem) {
                $eventDateTime = Carbon::parse($firstItem->scheduled_date . ' ' . $firstItem->scheduled_time);
                $totalAsli = $booking->total_price;
                
                if ($booking->payment_status == 'paid') {
                    $sudahBayar = $totalAsli;
                } elseif ($booking->payment_status == 'paid_dp' || $booking->payment_status == 'dp') {
                    $sudahBayar = ($booking->dp_amount > 0) ? $booking->dp_amount : ($totalAsli * 0.3);
                } else {
                    $sudahBayar = 0; 
                }
                
                $sisaPelunasan = max(0, $totalAsli - $sudahBayar);
                $isPastDay = Carbon::parse($firstItem->scheduled_date)->isBefore(Carbon::today());

                if ($booking->booking_status == 'completed' || $isPastDay) {
                    $bgColor = '#f3f4f6'; $borderColor = '#e5e7eb'; $textColor = '#6b7280'; 
                } elseif ($booking->payment_status == 'paid') {
                    $bgColor = '#d1fae5'; $borderColor = '#a7f3d0'; $textColor = '#065f46'; 
                } elseif ($booking->payment_status == 'paid_dp' || $booking->payment_status == 'dp') {
                    $bgColor = '#dbeafe'; $borderColor = '#bfdbfe'; $textColor = '#1e40af'; 
                } else {
                    $bgColor = '#fef3c7'; $borderColor = '#fde68a'; $textColor = '#92400e'; 
                }

                $isWalkIn = strpos($booking->invoice_code, 'WIN-') !== false;
                $customerName = $booking->user ? $booking->user->name : 'Tamu';
                if ($isWalkIn) $customerName .= ' (Walk-in)';

                $treatmentList = $booking->items->map(function($item) {
                    $nama = $item->treatment->name ?? $item->treatment->nama_layanan ?? 'Layanan Salon';
                    return '- ' . $nama; 
                })->implode('<br>');

                $formattedTime = $eventDateTime->translatedFormat('d M Y') . ' - ' . $eventDateTime->format('H:i') . ' WIB';
                $isWaiting = !in_array($booking->booking_status, ['completed', 'cancelled']);

                $events[] = [
                    'id' => $booking->id, 'title' => $customerName,
                    'start' => $firstItem->scheduled_date . 'T' . $firstItem->scheduled_time,
                    'backgroundColor' => $bgColor, 'borderColor' => $borderColor, 'textColor' => $textColor, 
                    'payment_status' => $booking->payment_status, 'booking_status' => $booking->booking_status,
                    'waktu_lengkap'  => $formattedTime, 'is_waiting' => $isWaiting, 'layanan' => $treatmentList,
                    'total_asli_raw' => $totalAsli, 'sudah_dp_raw' => $sudahBayar, 'sisa_raw' => $sisaPelunasan,
                    'total_asli' => number_format($totalAsli, 0, ',', '.'),
                    'sudah_dp' => number_format($sudahBayar, 0, ',', '.'),
                    'sisa' => number_format($sisaPelunasan, 0, ',', '.')
                ];
            }
        }

        $nextQueueObj = Booking::with(['user', 'items.treatment'])
            ->whereDate('booking_date', $todayDateStr)
            ->where('start_time', '>', $currentTimeStr)
            ->whereIn('payment_status', ['paid', 'paid_dp', 'pending'])
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('start_time', 'asc')
            ->first();
            
        if ($nextQueueObj) {
            $antreanSelanjutnyaWaktu = Carbon::parse($nextQueueObj->start_time)->format('H:i') . ' WIB';
            $isWalkIn = strpos($nextQueueObj->invoice_code, 'WIN-') !== false;
            $antreanSelanjutnyaPelanggan = $nextQueueObj->user ? $nextQueueObj->user->name : 'Tamu';
            if ($isWalkIn) $antreanSelanjutnyaPelanggan .= ' (Walk-in)';
            
            $antreanSelanjutnyaLayanan = $nextQueueObj->items->map(function($item) {
                return $item->treatment->name ?? 'Layanan';
            })->implode(', ');
        } else {
            $antreanSelanjutnyaWaktu = '0';
            $antreanSelanjutnyaPelanggan = '-';
            $antreanSelanjutnyaLayanan = '-';
        }

        // GRAFIK KEUANGAN
        $allTreatments = Treatment::all(); 
        $daysInMonth = $currentDate->daysInMonth;
        
        $revenueLabelsX = [];
        for ($i = 1; $i <= $daysInMonth; $i++) { 
            $revenueLabelsX[] = $i . ' ' . $currentDate->translatedFormat('M'); 
        }

        $chartDataFinance = [];
        $totalKeuanganBulanIni = Booking::whereMonth('booking_date', $selectedMonth)
            ->whereYear('booking_date', $selectedYear)
            ->whereIn('payment_status', ['paid', 'paid_dp', 'dp'])
            ->where('booking_status', '!=', 'cancelled')
            ->sum(DB::raw("CASE WHEN payment_status IN ('paid_dp', 'dp') THEN dp_amount WHEN payment_status = 'paid' THEN total_price ELSE 0 END"));

        foreach ($allTreatments as $treatment) {
            $dailyData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $income = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                    ->where('booking_items.treatment_id', $treatment->id)
                    ->whereYear('bookings.booking_date', $selectedYear)
                    ->whereMonth('bookings.booking_date', $selectedMonth)
                    ->whereDay('bookings.booking_date', $d)
                    ->whereIn('bookings.payment_status', ['paid', 'paid_dp', 'dp'])
                    ->where('bookings.booking_status', '!=', 'cancelled')
                    ->sum('booking_items.price_at_booking');
                
                $dailyData[] = (int)$income;
            }
            
            $color = $this->getTreatmentColor($treatment->name);
            $chartDataFinance[] = [
                'label' => $treatment->name,
                'data' => $dailyData,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'borderWidth' => 2, 
                'pointRadius' => 0, 
                'pointHoverRadius' => 6, 
                'pointHitRadius' => 15, 
                'fill' => false,
                'tension' => 0.4 
            ];
        }

        // LAYANAN POPULER & TOP 5
        $layananPopulerRaw = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('treatments', 'booking_items.treatment_id', '=', 'treatments.id')
            ->select('treatments.name', DB::raw('COUNT(booking_items.id) as total_dipesan'))
            ->whereMonth('bookings.booking_date', $selectedMonth)
            ->whereYear('bookings.booking_date', $selectedYear)
            ->where('bookings.booking_status', '!=', 'cancelled')
            ->groupBy('treatments.name')->orderBy('total_dipesan', 'desc')->get();

        $top5Layanan = $layananPopulerRaw->take(5);
        $pieLabels = []; $pieValues = []; $pieColors = []; $persentaseLayanan = [];
        $totalItemTerjual = $layananPopulerRaw->sum('total_dipesan');

        foreach ($layananPopulerRaw as $item) {
            $pieLabels[] = $item->name;
            $pieValues[] = $item->total_dipesan;
            $pieColors[] = $this->getTreatmentColor($item->name);
            $persentaseLayanan[] = $totalItemTerjual > 0 ? round(($item->total_dipesan / $totalItemTerjual) * 100) : 0;
        }

        return view('admin.dashboard', compact(
            'totalBookingsToday', 'antreanSelanjutnyaWaktu', 'antreanSelanjutnyaPelanggan', 'antreanSelanjutnyaLayanan', 
            'events', 'currentDate', 'prevMonth', 'nextMonth',
            'revenueLabelsX', 'chartDataFinance', 'totalKeuanganBulanIni',
            'pieLabels', 'pieValues', 'pieColors', 'persentaseLayanan', 'top5Layanan', 'totalItemTerjual'
        ));
    }

    private function getTreatmentColor($name) {
        $name = strtolower($name);
        if (str_contains($name, 'color')) return '#93c5fd'; 
        if (str_contains($name, 'haircut')) return '#c084fc'; 
        if (str_contains($name, 'cream')) return '#6ee7b7'; 
        if (str_contains($name, 'styling')) return '#fdba74'; 
        if (str_contains($name, 'mask')) return '#5eead4'; 
        if (str_contains($name, 'keratin')) return '#818cf8'; 
        
        $colors = ['#f472b6', '#fbbf24', '#38bdf8', '#fb7185', '#a78bfa']; 
        return $colors[crc32($name) % count($colors)];
    }

    // ====================================================================
    // 2. EXPORT PDF DINAMIS
    // ====================================================================
    public function exportPdf(Request $request)
    {
        $mode = $request->input('export_mode', 'current');
        $now = Carbon::now('Asia/Jakarta');
        $judulPeriode = "";

        if ($mode === 'current') {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
            $judulPeriode = "Bulan " . $startDate->translatedFormat('F Y');
        } elseif ($mode === 'specific') {
            $request->validate(['specific_month' => 'required']);
            $startDate = Carbon::createFromFormat('Y-m', $request->specific_month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $request->specific_month)->endOfMonth();
            $judulPeriode = "Bulan " . $startDate->translatedFormat('F Y');
        } elseif ($mode === 'range') {
            $request->validate(['start_month' => 'required', 'end_month' => 'required']);
            $startDate = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $request->end_month)->endOfMonth();
            $judulPeriode = $startDate->translatedFormat('F Y') . " - " . $endDate->translatedFormat('F Y');
        }

        $transaksi = Booking::with('user', 'items.treatment')
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('booking_date', 'asc')
            ->get();

        $totalPendapatan = $transaksi->sum(function($booking) {
            if (in_array($booking->payment_status, ['paid_dp', 'dp'])) return $booking->dp_amount;
            if ($booking->payment_status == 'paid') return $booking->total_price;
            return 0;
        });

        $treatments = Treatment::all();
        $chartLabels = [];
        $chartData = [];

        foreach ($treatments as $t) {
            $income = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->where('booking_items.treatment_id', $t->id)
                ->whereBetween('bookings.booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->whereIn('bookings.payment_status', ['paid', 'paid_dp', 'dp'])
                ->where('bookings.booking_status', '!=', 'cancelled')
                ->sum('booking_items.price_at_booking');
            
            if ($income > 0) {
                $chartLabels[] = $t->name;
                $chartData[] = $income;
            }
        }

        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $chartLabels,
                'datasets' => [[
                    'label' => 'Pendapatan (Rp)',
                    'data' => $chartData,
                    'backgroundColor' => '#db2777'
                ]]
            ],
            'options' => [
                'plugins' => [
                    'datalabels' => [
                        'align' => 'end', 'anchor' => 'end', 'color' => '#db2777',
                        'font' => ['weight' => 'bold']
                    ]
                ]
            ]
        ];
        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig)) . '&w=600&h=300';

        $pdf = Pdf::loadView('admin.pdf.report', compact('transaksi', 'totalPendapatan', 'judulPeriode', 'chartUrl'));
        
        $fileName = 'Laporan_NKBeautySalon_' . str_replace(' ', '_', $judulPeriode) . '.pdf';
        return $pdf->download($fileName);
    }

    // ====================================================================
    // 3. FITUR KASIR / POS & INVOICE
    // ====================================================================
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->booking_status = $request->status; 
        $booking->save();

        $pesan = $request->status == 'completed' ? 'Booking berhasil diselesaikan!' : 'Booking telah dibatalkan.';
        return back()->with('success', $pesan);
    }

    public function createWalkIn()
    {
        $treatments = Treatment::all();
        $users = User::where('role', 'user')->get(); 
        return view('admin.pos.create', compact('treatments', 'users'));
    }

    public function storeWalkIn(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'treatment_ids' => 'required|array', 
            'treatment_ids.*' => 'exists:treatments,id',
        ]);

        return DB::transaction(function () use ($request) {
            $treatments = Treatment::whereIn('id', $request->treatment_ids)->get();
            $totalPrice = $treatments->sum('price');
            $totalDuration = $treatments->sum('duration');

            $startTime = Carbon::parse($request->date . ' ' . $request->time);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            if ($request->filled('user_id')) {
                $customer = User::findOrFail($request->user_id);
            } else {
                $request->validate(['guest_name' => 'required|string', 'phone' => 'required|string']);
                $name = ucfirst(strtolower(trim($request->guest_name)));
                $phone = preg_replace('/[^0-9]/', '', $request->phone);
                if (substr($phone, 0, 1) == '0') { $phone = '62' . substr($phone, 1); }

                $customer = User::where('phone', $phone)->first() ?? User::create([
                    'name' => $name, 'phone' => $phone, 'email' => $phone . '@walkin.local',
                    'role' => 'user', 'password' => bcrypt(uniqid()),
                ]);
            }

            $booking = Booking::create([
                'invoice_code' => 'WIN-' . now()->format('YmdHis'),
                'user_id' => $customer->id, 'booking_date' => $request->date,
                'start_time' => $startTime->format('H:i:s'), 'end_time' => $endTime->format('H:i:s'),
                'total_price' => $totalPrice, 'dp_amount' => 0,
                'payment_status' => 'paid', 'booking_status' => 'confirmed',
            ]);

            $currentTime = $startTime->copy();
            foreach ($treatments as $treatment) {
                BookingItem::create([
                    'booking_id' => $booking->id, 'treatment_id' => $treatment->id,
                    'scheduled_date' => $request->date, 'scheduled_time' => $currentTime->format('H:i:s'),
                    'price_at_booking' => $treatment->price
                ]);
                $currentTime->addMinutes($treatment->duration);
            }

            return redirect()->route('admin.invoice', $booking->id);
        });
    }

    public function invoice($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);
        return view('admin.invoice.show', compact('booking'));
    }
}