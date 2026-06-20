<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Daftarkan middleware untuk controller ini (Standar Laravel 11+)
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'), // Mengunci semua method agar wajib login
            // Kunci kedua: Hanya izinkan jika pengguna adalah admin
            new Middleware(function (Request $request, $next) {
                if (!auth()->user()->is_admin) {
                    // Jika user biasa mencoba masuk, lempar balik ke dashboard dengan pesan error
                    return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Manajemen User!');
                }
                return $next($request);
            }),
        ];
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Jika user yang login BUKAN Master Admin (is_admin != 1)
            if (auth()->user()->is_admin != 1) {
                // Tendang kembali ke dashboard dengan pesan error
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman Manajemen User.');
            }
            return $next($request);
        });
    }

    // 1. TAMPILKAN DAFTAR USER
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // 2. SIMPAN USER BARU
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name'     => 'required|string|max:255',
            'nip'      => 'required|string|max:20|unique:users,nip',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'title'    => 'nullable|string|max:255', 
            'phone'    => 'nullable|string|max:20',
            'is_admin' => 'required|boolean',
            ], [
            'username.unique' => 'Data dengan username ini sudah tersedia.',
            'nip.unique'      => 'User dengan Nip ini sudah tersedia.',
            'email.unique'    => 'Alamat email ini sudah terdaftar di sistem.',
            'password.min'    => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);

        User::create([
            'username' => $request->username,
            'name'     => $request->name,
            'nip'      => $request->nip,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin'  => $request->is_admin,
            'is_active'=> true, 
            'title'    => $request->title,
            'phone'    => $request->phone,
        ]);

        return redirect()->back()->with('success', 'User baru berhasil ditambahkan!');
    }

    // 3. UPDATE DATA USER
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name'     => 'required|string|max:255',
            'nip'      => 'nullable|string|max:30|unique:users,nip,' . $user->id,
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'title'    => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'is_active'=> 'required|boolean',
            'is_admin' => 'required|boolean',
        ]);
        
        $user->username  = $request->username;
        $user->name      = $request->name;
        $user->nip       = $request->nip;
        $user->email     = $request->email;
        $user->title     = $request->title ?? '';
        $user->phone     = $request->phone ?? '';
        $user->is_active = $request->is_active; 
        $user->is_admin = $request->is_admin;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data user berhasil diperbarui!');
    }

    // 4. HAPUS USER
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->user()->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }
}