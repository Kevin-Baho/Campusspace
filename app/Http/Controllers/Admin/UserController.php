<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Users', [
            'users' => User::query()
                ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
                ->orderBy('name')->paginate(20)->withQueryString(),
            'q' => $request->string('q')->toString(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['student', 'staff', 'admin'])]]);
        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menurunkan role akun sendiri.');
        }
        $user->forceFill(['role' => $data['role']])->save(); // role tidak fillable, diset eksplisit
        return back()->with('success', 'Role diperbarui.');
    }
}
