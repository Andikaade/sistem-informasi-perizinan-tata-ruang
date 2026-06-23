<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets as GoogleSheetsService;

class LacakPerizinanController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function cari(Request $request)
    {
        $request->validate([
            'no_antrian' => 'required'
        ]);

        $keyword = trim($request->input('no_antrian'));

        try {
            $jsonPath = base_path('storage/app/google-sheets/credentials.json');

            if (!file_exists($jsonPath)) {
                return view('index', [
                    'error' => 'Berkas kunci keamanan (credentials.json) tidak ditemukan di folder storage.', 
                    'keyword' => $keyword
                ]);
            }

            $client = new Client();
            $client->setAuthConfig($jsonPath);
            $client->addScope(GoogleSheetsService::SPREADSHEETS_READONLY);

            $service = new GoogleSheetsService($client);
            $spreadsheetId = env('POST_SPREADSHEET_ID');
            
            $range = 'Sheet1!A:N'; 
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();

            if (empty($rows)) {
                return view('index', ['error' => 'Database di Google Sheets kosong atau range salah.', 'keyword' => $keyword]);
            }

            $hasilCari = null;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                //KOLOM A (INDEX 0) SEBAGAI NO_ANTRIAN
                $noAntrianDiSheets = $row[1] ?? ''; 

                if (trim($noAntrianDiSheets) === $keyword) {
                    $hasilCari = [
                        'no_antrian'        => $row[1] ?? '-', // Menyimpan nomor antrian
                        'no_surat'          => $row[3] ?? '-', 
                        'nama_pemohon'      => $row[2] ?? '-', 
                        'deskripsi_surat'   => $row[4] ?? '-', 
                        'phone'             => $row[5] ?? '-', 
                        'alamat'            => $row[6] ?? '-', 
                        'tgl_pengajuan'     => $row[8] ?? '-', 
                        'tgl_proses'        => $row[9] ?? '-', 
                        'tgl_selesai'       => $row[10] ?? '-', 
                        'status'            => $row[11] ?? '-', 
                    ];
                    break;
                }
            }

            if ($hasilCari) {
                return view('index', ['antrian' => $hasilCari, 'keyword' => $keyword]);
            }

            return view('index', ['error' => 'Nomor antrian tidak ditemukan.', 'keyword' => $keyword]);

        } catch (\Exception $e) {
            return view('index', ['error' => 'Gagal terhubung ke Google Sheets: ' . $e->getMessage(), 'keyword' => $keyword]);
        }
    }
}