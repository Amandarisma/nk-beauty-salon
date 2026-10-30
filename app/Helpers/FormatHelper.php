<?php

if (!function_exists('formatRupiahShort')) {
    function formatRupiahShort($angka)
    {
        // 🔥 AUTO FIX kalau data masih kecil (45 = 45rb)
        if ($angka < 1000) {
            $angka = $angka * 1000;
        }

        if ($angka >= 1000000) {
            $hasil = $angka / 1000000;
            return 'Rp ' . rtrim(rtrim(number_format($hasil, 1), '0'), '.') . 'JT';
        } elseif ($angka >= 1000) {
            return 'Rp ' . round($angka / 1000) . 'K';
        }

        return 'Rp ' . $angka;
    }
}