<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * RoleController - Mengelola data role user.
 *
 * Controller ini menangani CRUD role:
 * - Manajemen role (tambah, edit, hapus)
 * - Proteksi role default (manager, kasir)
 * - Validasi penghapusan role yang memiliki user
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class RoleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - LIST
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

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - CREATE
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form tambah role baru.
     *
     * @return View
     */
    public function create(): View
    {
        return view('manager.roles.create');
    }

    /**
     * Menyimpan role baru ke database.
     *
     * @param  Request $request Request dengan data role
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->route('manager.access.index')
            ->with('success', 'Role created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - EDIT & UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form edit role.
     *
     * @param  Role $role Role yang akan diedit
     * @return View
     */
    public function edit(Role $role): View
    {
        return view('manager.roles.edit', compact('role'));
    }

    /**
     * Mengupdate data role di database.
     *
     * @param  Request $request Request dengan data update
     * @param  Role    $role    Role yang akan diupdate
     * @return RedirectResponse
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('manager.access.index')
            ->with('success', 'Role updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - DELETE
    |--------------------------------------------------------------------------
    */

    /**
     * Menghapus role dari database.
     *
     * Proteksi:
     * - Role default (manager, kasir) tidak bisa dihapus
     * - Role yang memiliki user assigned tidak bisa dihapus
     *
     * @param  Role $role Role yang akan dihapus
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deletion of default roles
        if (in_array($role->name, ['manager', 'kasir'])) {
            return redirect()->route('manager.access.index')
                ->with('error', 'Cannot delete default role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('manager.access.index')
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('manager.access.index')
            ->with('success', 'Role deleted successfully.');
    }
}
