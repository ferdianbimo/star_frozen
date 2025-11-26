<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestAuthController extends Controller
{
    public function check()
    {
        $data = [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'user_email' => auth()->user()?->email,
            'role_name' => auth()->user()?->role?->name,
            'role_id' => auth()->user()?->role_id,
        ];
        
        return response()->json($data);
    }
}
