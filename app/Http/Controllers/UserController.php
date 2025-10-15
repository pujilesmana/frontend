<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function fetchUsers(Request $request)
    {
        // Ambil token dari session (diset setelah login)
        $token = session('tokenBackend');

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('Fetching users with token: ' . $token);
        // Panggil endpoint API yang diproteksi JWT
        $response = Http::withToken($token)->get(config('app.backend_url') . '/api/users');
        
        if ($response->failed()) {
            Log::error('Failed to fetch users: ' . $response->body());
            return response()->json(['message' => 'Gagal mengambil data user'], 500);
        }

        return response()->json($response->json());
    }
}
