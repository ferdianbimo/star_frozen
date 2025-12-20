<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to access control page instead
        return redirect()->route('manager.access.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('manager.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        return view('manager.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('manager.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->name ?? 'N/A',
        ];

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
