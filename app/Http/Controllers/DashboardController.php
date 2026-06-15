<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Inisialisasi Google Client
        $client = new Client();
        $client->setApplicationName('Tracking Perizinan');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));

        // 2. Inisialisasi Layanan Google Sheets
        $service = new Sheets($client);
        
        $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
        $range = 'Sheet1!A2:D'; 

        
        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();
        } catch (\Exception $e) {
            // Jika gagal atau error, set $rows sebagai array kosong agar tidak crash
            $rows = [];
        }
               
        // 3. Kirim data ke view dengan nama 'perizinanData' dan 'adminName'
        return view('dashboard', [
            'perizinanData' => $rows ?? [],
            'adminName' => auth()->user()->name ?? 'Admin' // Mengambil nama user login
        ]);
    } 
    public function updateStatus(Request $request)
{
    $request->validate([
        'row_index' => 'required|integer',
        'status' => 'required|string'
    ]);

    $rowIndex = $request->input('row_index');
    
    // Indeks tabel HTML mulai dari 0, sedangkan baris data pertama di spreadsheet 
    // biasanya berada di baris ke-2 (karena baris 1 digunakan untuk judul/header kolom).
    $spreadsheetRow = $rowIndex + 2; 

    // Ubah teks status agar memiliki huruf kapital di awal (misal: "selesai" -> "Selesai")
    $newStatus = ucfirst($request->input('status')); 

    try {
        // 1. Panggil konfigurasi service Google Client yang sudah kamu buat sebelumnya
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        
        $service = new \Google\Service\Sheets($client);
        
        // 2. ID Spreadsheet kamu (Silakan ganti dengan ID spreadsheet nyata milikmu)
        $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
        
        // 3. Tentukan koordinat kolom tempat Status berada.
        // Jika Status berada di kolom ke-4, maka koordinatnya adalah Kolom D (D + nomor baris)
        $range = "Sheet1!D" . $spreadsheetRow; 

        // 4. Bungkus nilai baru ke dalam objek ValueRange Google API
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [[$newStatus]]
        ]);

        // 5. Eksekusi perintah update ke Google Sheets server
        $service->spreadsheets_values->update(
            $spreadsheetId, 
            $range, 
            $body, 
            ['valueInputOption' => 'RAW']
        );

        // Jika berhasil, kembalikan ke halaman dengan membawa session alert sukses
        return redirect()->route('dashboard')->with('success', 'Status perizinan berhasil diperbarui langsung ke Google Sheets!');

    } catch (\Exception $e) {
        // Jika ada masalah koneksi/kredensial API, tangkap errornya agar tidak blank
        return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
    }
}
} 