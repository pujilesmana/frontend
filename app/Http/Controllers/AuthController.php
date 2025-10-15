<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi form
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            Log::info('Attempting login for user: ' . $request->email);
            Log::info('with url backend: ' . config('app.backend_url') . '/api/auth/token');
            // Kirim ke endpoint API menggunakan Http Client Laravel
            $response = Http::post(config('app.backend_url') . '/api/auth/token', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Simpan token ke session
                session(['tokenBackend' => $data['data']['access_token'] ?? null]);

                return redirect()->route('users.index')->with('success', 'Login berhasil!');
            } else {
                return back()->withErrors(['login' => 'Email atau password salah'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
