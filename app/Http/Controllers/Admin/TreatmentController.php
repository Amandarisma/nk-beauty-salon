<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TreatmentController extends Controller
{
    // 1. TAMPILKAN DAFTAR LAYANAN
    public function index()
    {
        $treatments = Treatment::all();
        return view('admin.treatments.index', compact('treatments'));
    }

    // 2. TAMPILKAN FORM TAMBAH
    public function create()
    {
        return view('admin.treatments.create');
    }

    // 3. PROSES SIMPAN DATA BARU
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer', // Dalam menit
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Siapkan data
        $data = $request->all();

        // Cek apakah ada upload gambar
        if ($request->hasFile('image')) {
            // Simpan gambar ke folder 'public/treatments'
            $data['image'] = $request->file('image')->store('treatments', 'public');
        }

        // Simpan ke Database
        Treatment::create($data);

        // Kembali ke halaman daftar dengan pesan sukses
        return redirect()->route('admin.treatments.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    // 4. TAMPILKAN FORM EDIT
    public function edit(Treatment $treatment)
    {
        return view('admin.treatments.edit', compact('treatment'));
    }

    // 5. PROSES UPDATE DATA
    public function update(Request $request, Treatment $treatment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Cek jika ada gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($treatment->image) {
                Storage::disk('public')->delete($treatment->image);
            }
            // Simpan gambar baru
            $data['image'] = $request->file('image')->store('treatments', 'public');
        }

        $treatment->update($data);

        return redirect()->route('admin.treatments.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    // 6. PROSES HAPUS DATA
    public function destroy(Treatment $treatment)
    {
        // Hapus gambar dari penyimpanan
        if ($treatment->image) {
            Storage::disk('public')->delete($treatment->image);
        }

        $treatment->delete();

        return redirect()->route('admin.treatments.index')->with('success', 'Layanan berhasil dihapus!');
    }
}
