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
        $token = session('tokenBackend');
        Log::info('Fetching users with token: ' . $token);
        if (!$token) return response()->json(['message' => 'Unauthorized'], 401);

        $response = Http::withToken($token)->get(config('app.backend_url') . '/api/users');
        if ($response->failed()) {
            Log::error('Failed to fetch users: ' . $response->body());
            return response()->json(['message' => 'Gagal mengambil data user', 'data'=>[]], 500);
        }
        return response()->json($response->json());
    }

    public function store(Request $request)
    {
        $token = session('tokenBackend');
        Log::info('Creating user with token: ' . $token);
        if (!$token) return response()->json(['message' => 'Unauthorized'], 401);

        Http::withToken($token)->post(config('app.backend_url') . '/api/users', $request->all());
        return redirect()->back()->with('status', 'User created!');
    }

    public function destroy($id)
    {
        $token = session('tokenBackend');  
        Log::info('Deleting user with token: ' . $token);
        if (!$token) return response()->json(['message' => 'Unauthorized'], 401);

        $response = Http::withToken($token)->delete(config('app.backend_url') . "/api/users/{$id}");
        return response()->json(['status' => $response->status()]);
    }
}
