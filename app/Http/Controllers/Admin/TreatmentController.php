<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::all();
        return view('admin.treatments.index', compact('treatments'));
    }

public function create()
{
    return view('admin.treatments.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable',
        'duration' => 'required|integer',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = $request->only([
        'name',
        'description',
        'duration',
        'price'
    ]);

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('treatments', 'public');
    }

    Treatment::create($data);

    return redirect()->route('admin.treatments.index')
        ->with('success', 'Layanan berhasil ditambahkan!');
}

    public function edit(Treatment $treatment)
    {
        return view('admin.treatments.edit', compact('treatment'));
    }

public function update(Request $request, Treatment $treatment)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable',
        'duration' => 'required|integer',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $data = $request->only([
        'name',
        'description',
        'duration',
        'price'
    ]);

    if ($request->hasFile('image')) {
        if ($treatment->image) {
            Storage::disk('public')->delete($treatment->image);
        }

        $data['image'] = $request->file('image')->store('treatments', 'public');
    }

    $treatment->update($data);

    return redirect()->route('admin.treatments.index')
        ->with('success', 'Layanan berhasil diperbarui!');
}

    public function destroy(Treatment $treatment)
    {
        if ($treatment->image) {
            Storage::disk('public')->delete($treatment->image);
        }
        $treatment->delete();
        return redirect()->route('admin.treatments.index')->with('success', 'Layanan berhasil dihapus!');
    }
}