<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Logout - Livewire Action untuk proses logout user.
 *
 * Class ini merupakan invokable action yang menangani
 * proses logout user dari aplikasi Star Frozen POS.
 *
 * Proses yang dilakukan:
 * 1. Logout dari guard 'web'
 * 2. Invalidate session untuk keamanan
 * 3. Regenerate CSRF token
 *
 * @package App\Livewire\Actions
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @example Penggunaan di Livewire Component
 * ```php
 * public function logout(Logout $logout)
 * {
 *     $logout();
 *     return redirect()->route('login');
 * }
 * ```
 */
class Logout
{
    /**
     * Melakukan logout user dari aplikasi.
     *
     * Method invokable yang:
     * - Logout dari guard 'web'
     * - Menghapus session
     * - Regenerate CSRF token untuk keamanan
     *
     * @return void
     */
    public function __invoke(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
