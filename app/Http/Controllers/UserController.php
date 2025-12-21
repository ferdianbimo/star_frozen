<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * UserController - Mengelola data user sistem.
 *
 * Controller ini menangani CRUD user dengan fitur:
 * - Manajemen user (tambah, edit, hapus)
 * - Assignment role (manager/kasir)
 * - Upload avatar user
 * - Pencatatan activity log
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - LIST & VIEW
    |--------------------------------------------------------------------------
    */

    /**
     * Redirect ke halaman access control.
     *
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        // Redirect to access control page instead
        return redirect()->route('manager.access.index');
    }

    /**
     * Menampilkan detail user.
     *
     * @param  User $user User yang akan ditampilkan
     * @return View
     */
    public function show(User $user): View
    {
        $user->load('role');
        return view('manager.users.show', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - CREATE
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form tambah user baru.
     *
     * @return View
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('manager.users.create', compact('roles'));
    }

    /**
     * Menyimpan user baru ke database.
     *
     * Memproses:
     * - Validasi input (nama, email, password, role)
     * - Upload avatar jika ada
     * - Hash password
     * - Pencatatan activity log
     *
     * @param  Request $request Request dengan data user
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Log activity
        $role = Role::find($validated['role_id']);
        ActivityLogService::logCreate(
            'user',
            "Menambahkan user baru: {$user->name} (Role: {$role->name})",
            $user,
            ['name' => $user->name, 'email' => $user->email, 'role' => $role->name]
        );

        return redirect()->route('manager.access.index')
            ->with('success', 'User created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - EDIT & UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form edit user.
     *
     * @param  User $user User yang akan diedit
     * @return View
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('manager.users.edit', compact('user', 'roles'));
    }

    /**
     * Mengupdate data user di database.
     *
     * Memproses:
     * - Validasi input
     * - Upload avatar baru jika ada (hapus yang lama)
     * - Update password jika diisi
     * - Pencatatan activity log dengan old/new values
     *
     * @param  Request $request Request dengan data update
     * @param  User    $user    User yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name ?? 'N/A',
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Handle avatar removal
        if ($request->has('remove_avatar') && $request->remove_avatar) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load('role');

        // Log activity
        ActivityLogService::logUpdate(
            'user',
            "Mengubah user: {$user->name}",
            $user,
            $oldValues,
            ['name' => $user->name, 'email' => $user->email, 'role' => $user->role->name ?? 'N/A']
        );

        return redirect()->route('manager.access.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('manager.access.index')
                ->with('error', 'Cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role->name ?? 'N/A';

        $user->delete();

        // Log activity
        ActivityLogService::logDelete(
            'user',
            "Menghapus user: {$userName}",
            null,
            ['name' => $userName, 'email' => $userEmail, 'role' => $userRole]
        );

        return redirect()->route('manager.access.index')
            ->with('success', 'User deleted successfully.');
    }
}
