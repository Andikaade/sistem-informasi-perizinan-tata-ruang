<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KontakController extends Controller
{
    public function kirimPesan(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subjek'  => 'required|string|max:150',
            'pesan'   => 'required|string',
        ]);

        // 2. Bungkus Data Inputan
        $dataPesan = [
            'nama'   => $request->nama,
            'email'  => $request->email,
            'subjek' => $request->subjek,
            'pesan'  => $request->pesan,
        ];
        
        // 3. Kirim Email Menggunakan Fitur Mail Laravel
        // dinas@sijunjung.go.id ganti dengan email instansi 
        Mail::send([], [], function ($message) use ($dataPesan) {
            $message->to('dinas@sijunjung.go.id') 
                    ->subject('[Website Tata Ruang] ' . $dataPesan['subjek'])
                    ->from($dataPesan['email'], $dataPesan['nama'])
                    ->html("
                        <h3>Pesan Baru dari Website Perizinan</h3>
                        <p><strong>Nama Pengirim:</strong> {$dataPesan['nama']}</p>
                        <p><strong>Email Pengirim:</strong> {$dataPesan['email']}</p>
                        <p><strong>Isi Pesan:</strong></p>
                        <p style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>{$dataPesan['pesan']}</p>
                    ");
        });

        // 4. Redirect kembali dengan Alert Sukses
        return redirect()->back()->with('success_email', 'Pesan Anda berhasil dikirim ke email resmi dinas!');
    }
}
