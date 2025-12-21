<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AccessController - Mengelola access control sistem.
 *
 * Controller ini menangani manajemen user dan role:
 * - Dashboard access control dengan statistik
 * - Daftar user dengan role
 * - Daftar role dengan jumlah user
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class AccessController extends Controller
{
    /**
     * Menampilkan dashboard access control.
     *
     * Menampilkan:
     * - Daftar user dengan pagination
     * - Daftar role dengan user count
     * - Statistik: total users, total roles, manager count, cashier count
     *
     * @return View
     */
    public function index(): View
    {
        $users = User::with('role')->paginate(15);
        $roles = Role::withCount('users')->get();
        
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'manager_count' => User::whereHas('role', function($q) {
                $q->where('name', 'manager');
            })->count(),
            'cashier_count' => User::whereHas('role', function($q) {
                $q->where('name', 'kasir');
            })->count(),
        ];

        return view('manager.access.index', compact('users', 'roles', 'stats'));
    }
}
