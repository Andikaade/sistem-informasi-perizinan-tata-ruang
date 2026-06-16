<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
        
        // PERBAIKAN 1: Lebarkan range pengambilan data dari A2:D menjadi A2:K 
        // Agar kolom status (Kolom K) ikut terbaca oleh aplikasi Laravel
        $range = 'Sheet1!A2:K'; 

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

        // Catatan: Kode pagination di bawah ini tidak akan pernah dieksekusi 
        // karena terkena statement 'return view' di atasnya. 
        // Jika ingin menggunakan pagination, silakan pindahkan return view-nya ke bawah.
        $perPage = 10; 
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($allRows);

        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        $paginatedItems = new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        $paginatedItems->setPath($request->url());

        return view('dashboard', ['dataPerizinan' => $paginatedItems]);
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
            
            // PERBAIKAN 2: Ubah range simpan status dari kolom D ke kolom K
            $range = "Sheet1!K" . $spreadsheetRow; 

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
            
            // PERBAIKAN 3: Sesuai struktur baru, Nama Pemohon ada di kolom C dan No Surat ada di kolom D
            $range = "Sheet1!C" . $spreadsheetRow . ":D" . $spreadsheetRow; 

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
            $client = new \Google\Client();
            $client->setApplicationName('Tracking Perizinan');
            $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
            $client->setAuthConfig(storage_path('app/google-sheets/credentials.json'));

            $service = new \Google\Service\Sheets($client);
            
            $spreadsheetId = '1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'; 
            $sheetName = 'Sheet1'; 

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

            $realRowIndex = (int)$index + 1; 

            $requestBody = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
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

            $service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);

            return redirect()->route('dashboard')->with('success', 'Data perizinan berhasil dihapus dari Google Sheets!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}