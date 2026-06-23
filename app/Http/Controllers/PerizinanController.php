<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;

class PerizinanController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_antrian'      => 'required',
            'nama_pemohon'    => 'required', 
            'no_surat'        => 'required',
            'phone'           => 'required',
            'deskripsi_surat' => 'required', 
            'alamat'          => 'required',
        ]);

        // 2. Data otomatis pendukung
        $createdBy    = auth()->user()?->name ?? 'admin';
        $tglPengajuan = now()->format('d-M-Y'); 
        $status       = 'Proses'; 

        try {
            // 3. Inisialisasi Google Client
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS);

            $service = new Sheets($client);
            
            $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
            $range = 'Sheet1!A:L'; // Mengunci range input data utama

            
            $rowValues = [
                "",                                   // Kolom A: No (Biar otomatis urutan row sheet / diisi manual nanti)
                $validatedData['no_antrian'],         
                $validatedData['nama_pemohon'],       
                $validatedData['no_surat'],           
                $validatedData['deskripsi_surat'],     
                $validatedData['phone'],              
                $validatedData['alamat'],             
                $createdBy,                           
                $tglPengajuan,                        
                "",                                   
                "",                                   
                $status                               
            ];

            $body = new Sheets\ValueRange([
                'values' => [$rowValues]
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            // Eksekusi penambahan baris ke Google Sheets
            $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan langsung ke Google Spreadsheet!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim data ke Spreadsheet: ' . $e->getMessage()
            ], 500);
        }
    }
}