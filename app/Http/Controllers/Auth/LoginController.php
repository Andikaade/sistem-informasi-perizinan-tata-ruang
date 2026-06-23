<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Google\Client;
use Google\Service\Sheets;

class LoginController extends Controller
{
   // 1. Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

   // 2. Memproses data dari form login
    public function login(Request $request)
    {
        // Validasi input form
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Proses autentikasi langsung mencocokkan ke database
        if (Auth::attempt($credentials)) {
            
            // PROTEKSI KEAMANAN: Cek apakah akun user dalam status Non-Aktif
            if (!auth()->user()->is_active) {
                // Keluarkan kembali secara paksa dari sesi autentikasi
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Kembalikan ke halaman login dengan pesan error khusus status akun
                return redirect()->route('login')->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan oleh Administrator. Silakan hubungi staff IT.',
                ])->onlyInput('username');
            }

            // Jika akun aktif, regenerasi session dan izinkan masuk ke dashboard
            $request->session()->regenerate();
        
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembali dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    // 3. Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}