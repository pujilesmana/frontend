<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

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

    //from api
    // public function generateQrCode($id)
    // {
    //     $token = session('tokenBackend');  
    //     Log::info('Generating QR code for user with token: ' . $token);
    //     if (!$token) return response()->json(['message' => 'Unauthorized'], 401);

    //     $response = Http::withToken($token)->post(config('app.backend_url') . "/api/generate-qr", ['text' => $id]);
    //     if ($response->failed()) {
    //         Log::error('Failed to generate QR code: ' . $response->body());
    //         return response()->json(['message' => 'Gagal menghasilkan QR code'], 500);
    //     }
    //     return response()->json($response->json());
    // }

    public function generateQrCode(Request $request){
        $validated = $request->validate([
            'id' => 'required',
        ]);

        $id = $validated['id'];

        // Generate QR ke PNG base64
        $image = QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($id);

        // Convert ke base64 untuk tag <img>
        $qr = 'data:image/png;base64,' . base64_encode($image);

        return response()->json(['qr' => $qr]);
    }

    public function generateBarcode(Request $request){
        $validated = $request->validate([
            'id' => 'required',
        ]);

        $id = $validated['id'];

        // Generate barcode (QR Code)
        $png = DNS1D::getBarcodePNG($id, 'C39');

        // Ubah ke Base64 agar bisa digunakan di tag <img>
        $bar = 'data:image/png;base64,' . $png;

        return response()->json(['bar' => $bar]);
    }
}
