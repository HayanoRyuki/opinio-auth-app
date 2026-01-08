<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name'  => 'required|string|max:255',
        ]);

        $user = User::create([
            'id'       => (string) Str::uuid(),
            'email'    => $data['email'],
            'name'     => $data['name'],
            'password' => Str::random(32), // 初期はダミー
        ]);

        return response()->json([
            'id'    => $user->id,
            'email' => $user->email,
        ], 201);
    }
}
