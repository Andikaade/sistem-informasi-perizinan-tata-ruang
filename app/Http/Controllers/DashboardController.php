<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Ditambahkan untuk memastikan facade Validator terpanggil dengan benar

class DashboardController extends Controller
{
    private $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs';

    /**
     * Helper untuk inisialisasi Google Sheets Service secara efisien
     */
    private function getGoogleSheetsService()
    {
        $client = new Client();
        $client->setApplicationName('Tracking Perizinan');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));

        return new Sheets($client);
    }

    /**
     * Generate Nomor Antrian otomatis dari data Google Sheets
     */
    public function generateNoAntrian()
    {
        try {
            $service = $this->getGoogleSheetsService();
            $range = 'Sheet1!B2:B'; 
            
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $rows = $response->getValues();

            $maxNumber = 0;

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $noAntrian = $row[0] ?? ''; 

                    if (!empty($noAntrian)) {
                        $numericPart = (int) filter_var($noAntrian, FILTER_SANITIZE_NUMBER_INT);
                        if ($numericPart > $maxNumber) {
                            $maxNumber = $numericPart;
                        }
                    }
                }
            }

            $nextNumber = $maxNumber + 1;
            $nextAntrian = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'no_antrian' => $nextAntrian
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor antrian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $service = $this->getGoogleSheetsService();
        $range = 'Sheet1!A2:N'; 

        try {
            $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
            $rows = $response->getValues();
        } catch (\Exception $e) {
            $rows = [];
        }
                
        return view('dashboard', [
            'perizinanData' => $rows ?? [],
            'adminName' => auth()->user()->name ?? 'Admin'
        ]);
    } 

    public function updateStatus(Request $request)
    {
        $request->validate([
            'row_index' => 'required|integer',
            'status' => 'required|string'
        ]);

        $rowIndex = $request->input('row_index');
        $spreadsheetRow = (int)$rowIndex + 2; 

        try {
            $service = $this->getGoogleSheetsService();
            $statusInput = trim(strtolower($request->input('status')));
            
            if ($statusInput === 'proses') {
                $newStatus = 'Dalam Proses';
            } else {
                $newStatus = ucfirst($statusInput);
            }

            // 1. UPDATE STATUS UTAMA (Kolom L)
            $rangeStatus = "Sheet1!L" . $spreadsheetRow; 
            $bodyStatus = new ValueRange([
                'values' => [[$newStatus]]
            ]);
            $service->spreadsheets_values->update($this->spreadsheetId, $rangeStatus, $bodyStatus, ['valueInputOption' => 'RAW']);

            $dateFormatted = now()->format('j-M-Y'); 
            
            if ($statusInput === 'proses') {
                $rangeProses = "Sheet1!J" . $spreadsheetRow;
                $bodyProses = new ValueRange([
                    'values' => [[$dateFormatted]]
                ]);
                $service->spreadsheets_values->update($this->spreadsheetId, $rangeProses, $bodyProses, ['valueInputOption' => 'RAW']);
                
            } elseif ($statusInput === 'selesai' || $statusInput === 'dikembalikan') {
                $rangeSelesai = "Sheet1!K" . $spreadsheetRow;
                $bodySelesai = new ValueRange([
                    'values' => [[$dateFormatted]]
                ]);
                $service->spreadsheets_values->update($this->spreadsheetId, $rangeSelesai, $bodySelesai, ['valueInputOption' => 'RAW']);
            }

            return redirect()->route('dashboard')->with('success', 'Status perizinan dan tanggal alur berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

   public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'row_index'       => 'required',
            'nama_pemohon'    => 'required|string',
            'no_surat'        => 'required|string',
            'deskripsi_surat' => 'required|string',
            'phone'           => 'required|string',
            'alamat'          => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

       $rowIndex = (int)$request->input('row_index');
        $spreadsheetRow = $rowIndex + 2; 

        try {
            $service = $this->getGoogleSheetsService();
            
            // Tambahkan user yang sedang login dan tanggal saat ini
            $namaAdmin = auth()->user()->name ?? 'System';
            $tanggalSekarang = now()->format('d-m-Y H:i'); 

            $values = [
                $request->input('nama_pemohon'),
                $request->input('no_surat'),
                $request->input('deskripsi_surat'),
                $request->input('phone'),
                $request->input('alamat'),
                $namaAdmin,         // <--- TAMBAHKAN INI (Sesuaikan kolomnya)
                $tanggalSekarang    // <--- TAMBAHKAN INI (Sesuaikan kolomnya)
            ];

            $bodyData = new ValueRange(['values' => [$values]]);

            // Contoh: Jika data awal C:G, maka sekarang harus C:I
            $rangeData = "Sheet1!C{$spreadsheetRow}:I{$spreadsheetRow}"; 
            
            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                $rangeData, 
                $bodyData, 
                ['valueInputOption' => 'USER_ENTERED'] // Gunakan USER_ENTERED agar format tanggal terbaca
            );

            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate!'], 200);

        } catch (\Exception $e) {
            \Log::error("Error Update: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function destroy($index)
    {
        try {
            $service = $this->getGoogleSheetsService();
            $sheetName = 'Sheet1'; 

            $spreadsheet = $service->spreadsheets->get($this->spreadsheetId);
            $sheetId = null;
            foreach ($spreadsheet->getSheets() as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetId = $sheet->getProperties()->getSheetId();
                    break;
                }
            }

            if (is_null($sheetId)) {
                return redirect()->back()->with('error', "Sheet dengan nama '{$sheetName}' tidak ditemukan.");
            }

            $realRowIndex = (int)$index + 1; 

            $requestBody = new BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'deleteDimension' => [
                        'range' => [
                            'sheetId'    => $sheetId,
                            'dimension'  => 'ROWS',
                            'startIndex' => $realRowIndex,     
                            'endIndex'   => $realRowIndex + 1  
                        ]
                    ]
                ]
            ]);

            $service->spreadsheets->batchUpdate($this->spreadsheetId, $requestBody);

            return redirect()->route('dashboard')->with('success', 'Data perizinan berhasil dihapus dari Google Sheets!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}