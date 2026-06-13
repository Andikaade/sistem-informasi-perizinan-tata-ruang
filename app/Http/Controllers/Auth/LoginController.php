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

   // 2. Memproses data dari form login (Standar MySQL)
    public function login(Request $request)
    {
        // Validasi input form
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Proses autentikasi langsung mencocokkan ke database MySQL Laragon
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
        
            // Jika sukses, masuk ke halaman dashboard admin
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
