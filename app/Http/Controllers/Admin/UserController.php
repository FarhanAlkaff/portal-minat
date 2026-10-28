<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('grades')->orderByDesc('id')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $grades = Grade::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.users.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:super_admin,grade_admin'],
            'grade_ids' => ['required_if:role,grade_admin', 'nullable', 'array'],
            'grade_ids.*' => ['exists:grades,id'],
        ], [
            'grade_ids.required_if' => 'Pilih minimal satu jenjang/grade untuk peran Admin Grade.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        if ($validated['role'] === 'grade_admin' && !empty($validated['grade_ids'])) {
            $user->grades()->sync($validated['grade_ids']);
        }

        AuditLogger::log("Menambahkan pengguna baru: {$user->email} ({$user->role})", $user);

        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $grades = Grade::where('is_active', true)->orderBy('sort_order')->get();
        $userGradeIds = $user->grades->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'grades', 'userGradeIds'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:super_admin,grade_admin'],
            'grade_ids' => ['required_if:role,grade_admin', 'nullable', 'array'],
            'grade_ids.*' => ['exists:grades,id'],
        ], [
            'grade_ids.required_if' => 'Pilih minimal satu jenjang/grade untuk peran Admin Grade.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($validated['role'] === 'grade_admin') {
            $user->grades()->sync($validated['grade_ids'] ?? []);
        } else {
            $user->grades()->detach();
        }

        AuditLogger::log("Memperbarui akun admin: {$user->email}", $user);

        return redirect()->route('admin.users.index')->with('success', 'Data akun admin berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya akun Super Admin.');
        }

        $email = $user->email;
        $user->grades()->detach();
        $user->delete();

        AuditLogger::log("Menghapus akun admin: {$email}");

        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
