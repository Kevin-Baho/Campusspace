<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityAmenity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FacilityController extends Controller
{
    private function rules(): array
    {
        return [
            'category' => ['required', Rule::in(['library', 'saung', 'room'])], 'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'], 'location' => ['nullable', 'string', 'max:255'], 'floor' => ['nullable', 'string', 'max:20'],
            'capacity' => ['nullable', 'integer', 'min:1'], 'image_url' => ['nullable', 'url', 'max:255'], 'is_active' => ['boolean'],
            'amenities' => ['array'], 'amenities.*' => ['string', 'max:100'],
        ];
    }

    public function index()
    {
        return Inertia::render('Admin/Facilities', ['facilities' => Facility::with('amenities')->withCount('units')->orderBy('category')->orderBy('name')->paginate(20)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $f = Facility::create(collect($data)->except('amenities')->all());
        $this->syncAmenities($f, $data['amenities'] ?? []);
        return back()->with('success', 'Fasilitas dibuat.');
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate($this->rules());
        $facility->update(collect($data)->except('amenities')->all());
        $this->syncAmenities($facility, $data['amenities'] ?? []);
        return back()->with('success', 'Fasilitas diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->units()->whereHas('bookings', fn ($q) => $q->active())->exists()) {
            return back()->with('error', 'Masih ada booking aktif pada unit fasilitas ini.');
        }
        $facility->update(['is_active' => false]); // nonaktifkan, jangan hapus riwayat
        return back()->with('success', 'Fasilitas dinonaktifkan.');
    }

    private function syncAmenities(Facility $f, array $names): void
    {
        $f->amenities()->whereNotIn('name', $names)->delete();
        foreach ($names as $n) FacilityAmenity::firstOrCreate(['facility_id' => $f->id, 'name' => $n]);
    }
}
