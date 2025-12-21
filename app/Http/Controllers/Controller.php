<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller - Base controller untuk semua controller aplikasi.
 *
 * Controller ini menyediakan trait dasar untuk otorisasi
 * dan validasi yang digunakan oleh semua controller turunan.
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
