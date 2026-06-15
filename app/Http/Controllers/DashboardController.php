<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

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

    public function index()
    {
        $service = $this->getGoogleSheetsService();
        $range = 'Sheet1!A2:D'; 

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
        $spreadsheetRow = $rowIndex + 2; 
        $newStatus = ucfirst($request->input('status')); 

        try {
            $service = $this->getGoogleSheetsService();
            $range = "Sheet1!D" . $spreadsheetRow; 

            $body = new \Google\Service\Sheets\ValueRange([
                'values' => [[$newStatus]]
            ]);

            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );

            return redirect()->route('dashboard')->with('success', 'Status perizinan berhasil diperbarui langsung ke Google Sheets!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function updateData(Request $request)
    {
        $request->validate([
            'row_index' => 'required|integer',
            'nama' => 'required|string',
            'no_surat' => 'required|string',
        ]);

        $rowIndex = $request->input('row_index');
        $spreadsheetRow = $rowIndex + 2; 

        try {
            $service = $this->getGoogleSheetsService();
            $range = "Sheet1!B" . $spreadsheetRow . ":C" . $spreadsheetRow; 

            $body = new \Google\Service\Sheets\ValueRange([
                'values' => [[
                    $request->input('nama'),
                    $request->input('no_surat')
                ]]
            ]);

            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );

            return redirect()->route('dashboard')->with('success', 'Data administrasi pemohon berhasil diperbarui di Google Sheets!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($index)
    {
        try {
            // 1. Inisialisasi Google Client secara langsung di dalam method
            $client = new \Google\Client();
            $client->setApplicationName('Tracking Perizinan');
            $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
            $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));

            // 2. Inisialisasi Layanan Google Sheets
            $service = new \Google\Service\Sheets($client);
            
            // Gunakan string ID Spreadsheet Anda langsung secara benar
            $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
            $sheetName = 'Sheet1'; 

            // 3. Mengambil metadata spreadsheet untuk mendapatkan sheetId numerik
            $spreadsheet = $service->spreadsheets->get($spreadsheetId);
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

            // 4. Hitung posisi indeks asli baris di Google Sheets API
            // Jika $index dari tabel adalah 0 (baris pertama setelah header), 
            // maka di API Google, indeks baris data pertama tersebut berada di baris indeks 1.
            $realRowIndex = (int)$index + 1; 

            // 5. Susun Request Batch Update untuk menghapus baris secara fisik
            $requestBody = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'deleteDimension' => [
                        'range' => [
                            'sheetId'    => $sheetId,
                            'dimension'  => 'ROWS',
                            'startIndex' => $realRowIndex,     // Baris awal yang dihapus (inklusif)
                            'endIndex'   => $realRowIndex + 1  // Batas akhir hapus (eksklusif)
                        ]
                    ]
                ]
            ]);

            // 6. Eksekusi penghapusan ke Google Sheets API
            $service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);

            return redirect()->route('dashboard')->with('success', 'Data perizinan berhasil dihapus dari Google Sheets!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}