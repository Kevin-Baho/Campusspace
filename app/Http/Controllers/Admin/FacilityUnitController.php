<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FacilityUnitController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Units', [
            'units' => FacilityUnit::with('facility:id,name,category')->orderBy('code')->paginate(25),
            'facilities' => Facility::orderBy('name')->get(['id', 'name', 'category']),
        ]);
    }

    public function store(Request $request, QRCodeService $qr)
    {
        $data = $request->validate($this->rules());
        $unit = new FacilityUnit($data);
        $unit->forceFill(['qr_token' => $qr->newToken()])->save();
        return back()->with('success', 'Unit dibuat.');
    }

    public function update(Request $request, FacilityUnit $unit)
    {
        $unit->update($request->validate($this->rules($unit->id)));
        return back()->with('success', 'Unit diperbarui.');
    }

    public function destroy(FacilityUnit $unit)
    {
        if ($unit->bookings()->active()->exists()) return back()->with('error', 'Unit masih memiliki booking aktif.');
        $unit->update(['is_active' => false]);
        return back()->with('success', 'Unit dinonaktifkan.');
    }

    private function rules(?int $ignore = null): array
    {
        return [
            'facility_id' => ['required', 'exists:facilities,id'],
            'code' => ['required', 'string', 'max:40', Rule::unique('facility_units', 'code')->ignore($ignore)],
            'name' => ['nullable', 'string', 'max:255'], 'capacity' => ['nullable', 'integer', 'min:1'],
            'floor' => ['nullable', 'string', 'max:20'], 'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['available', 'maintenance'])],
            'view_images' => ['nullable', 'array'], 'view_images.*' => ['url', 'max:500'], 'is_active' => ['boolean'],
        ];
    }
}
