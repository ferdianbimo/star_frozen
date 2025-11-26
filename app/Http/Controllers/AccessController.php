<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /**
     * Display access control dashboard.
     */
    public function index()
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
