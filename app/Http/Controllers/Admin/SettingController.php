<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    private const KEYS = ['min_duration_minutes', 'max_duration_minutes', 'checkin_grace_minutes', 'checkin_open_before_minutes'];

    public function index()
    {
        return Inertia::render('Admin/Settings', ['settings' => collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => SystemSetting::get($k)])]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'min_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'max_duration_minutes' => ['nullable', 'integer', 'min:1', 'gte:min_duration_minutes'],
            'checkin_grace_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'checkin_open_before_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
        ]);
        foreach (self::KEYS as $k) SystemSetting::put($k, $data[$k] ?? null);
        return back()->with('success', 'Pengaturan disimpan.');
    }
}
