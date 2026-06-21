<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;

class PerizinanController extends Controller
{
    public function store(Request $request)
    {
        // 1. SESUAIKAN VALIDASI: Menyamakan key dengan atribut 'name' pada Form HTML kamu
        $validatedData = $request->validate([
            'no_antrian'      => 'required',
            'nama_pemohon'    => 'required', // Sebelumnya 'nama'
            'no_surat'        => 'required',
            'phone'           => 'required',
            'deskripsi_surat' => 'required', // Sebelumnya 'deskripsi'
            'alamat'          => 'required',
        ]);

        // 2. Data otomatis pendukung
        $createdBy    = auth()->user()?->name ?? 'admin';
        $tglPengajuan = now()->format('d-M-Y'); // Menggunakan format DD-Mmm-YYYY (Contoh: 22-Jun-2026)
        $status       = 'Proses'; // Disesuaikan dengan string "Proses" di Google Sheets kamu

        try {
            // 3. Inisialisasi Google Client
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS);

            $service = new Sheets($client);
            
            $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
            $range = 'Sheet1!A:L'; // Mengunci range input data utama

            // 4. SUSUN MAPPING BARIS (Diselaraskan dengan urutan kolom Google Sheets kamu)
            // Kolom A=No Urut, B=No Antrian, C=Nama, D=No Surat, E=Perihal, F=No HP, G=Alamat, H=Admin, I=Tgl1, J=Tgl2, K=Tgl3, L=Status
            $rowValues = [
                "",                                   // Kolom A: No (Biar otomatis urutan row sheet / diisi manual nanti)
                $validatedData['no_antrian'],         // Kolom B: No Antrian
                $validatedData['nama_pemohon'],       // Kolom C: Nama Pemohon
                $validatedData['no_surat'],           // Kolom D: Nomor Surat
                $validatedData['deskripsi_surat'],     // Kolom E: Deskripsi / Perihal Perizinan
                $validatedData['phone'],              // Kolom F: No. HP / WhatsApp
                $validatedData['alamat'],             // Kolom G: Alamat Pemohon
                $createdBy,                           // Kolom H: Created By (Di sheet kamu isinya 'admin')
                $tglPengajuan,                        // Kolom I: Tanggal Pengajuan / Proses Pertama
                "",                                   // Kolom J: Kosong (Untuk timeline tgl_proses berikutnya)
                "",                                   // Kolom K: Kosong (Untuk timeline tgl_selesai)
                $status                               // Kolom L: Status ("Proses")
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