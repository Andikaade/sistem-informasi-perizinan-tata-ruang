<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
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
        $spreadsheetRow = $rowIndex; 

        try {
            $service = $this->getGoogleSheetsService();
            $statusInput = trim(strtolower($request->input('status')));
            
            if ($statusInput === 'proses') {
                $newStatus = 'Dalam Proses';
            } else {
                $newStatus = ucfirst($statusInput); // "Selesai" atau "Dikembalikan"
            }

            // 1. UPDATE STATUS UTAMA (Kolom L)
            $rangeStatus = "Sheet1!L" . $spreadsheetRow; 
            $bodyStatus = new \Google\Service\Sheets\ValueRange([
                'values' => [[$newStatus]]
            ]);
            $service->spreadsheets_values->update($this->spreadsheetId, $rangeStatus, $bodyStatus, ['valueInputOption' => 'RAW']);

            // Format tanggal pengajuan/proses/selesai (Template default: 18-Jun-2026)
            $dateFormatted = now()->format('j-M-Y'); 
            
    
            if ($statusInput === 'proses') {
                // Isi Kolom J (tgl_proses)
                $rangeProses = "Sheet1!J" . $spreadsheetRow;
                $bodyProses = new \Google\Service\Sheets\ValueRange([
                    'values' => [[$dateFormatted]]
                ]);
                $service->spreadsheets_values->update($this->spreadsheetId, $rangeProses, $bodyProses, ['valueInputOption' => 'RAW']);
                
            } elseif ($statusInput === 'selesai' || $statusInput === 'dikembalikan') {
                $rangeSelesai = "Sheet1!K" . $spreadsheetRow;
                $bodySelesai = new \Google\Service\Sheets\ValueRange([
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
        // 1. Validasi semua input yang dikirimkan dari modal form edit
        $request->validate([
            'row_index'           => 'required|integer',
            'nama_pemohon'        => 'required|string',
            'no_surat'            => 'required|string',
            'deskripsi_surat'     => 'required|string',
            'phone'               => 'required|string',
            'alamat'              => 'required|string',
            'captcha_jawaban'     => 'required|integer',
            'password_konfirmasi' => 'required|string',
        ]);

        // 2. Proteksi Keamanan: Verifikasi kesesuaian password akun dengan admin yang login
        if (!Hash::check($request->input('password_konfirmasi'), auth()->user()->password)) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: Konfirmasi password yang Anda masukkan salah!');
        }

        $spreadsheetRow = $request->input('row_index');

        try {
            $service = $this->getGoogleSheetsService();
            
            // 3. UPDATE DATA UTAMA (Kolom C sampai G)
            // C: nama_pemohon, D: no_surat, E: deskripsi_surat, F: phone, G: alamat
            $rangeData = "Sheet1!C" . $spreadsheetRow . ":G" . $spreadsheetRow; 

            $bodyData = new ValueRange([
                'values' => [[
                    $request->input('nama_pemohon'),
                    $request->input('no_surat'),
                    $request->input('deskripsi_surat'),
                    $request->input('phone'),
                    $request->input('alamat')
                ]]
            ]);
            
            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                $rangeData, 
                $bodyData, 
                ['valueInputOption' => 'RAW']
            );

            // 4. FORMAT LOG RIWAYAT (Kolom M dan N)
            // Menyertakan jam menit (H:i) agar sesuai dengan format log edit di spreadsheet Anda
            $dateTimeLog = now()->format('d/m/Y H:i'); 
            $adminName = auth()->user()->name ?? 'admin';

            $rangeAudit = "Sheet1!M" . $spreadsheetRow . ":N" . $spreadsheetRow;

            $bodyAudit = new ValueRange([
                'values' => [[
                    $adminName,   // Kolom M: Diisi Nama Admin
                    $dateTimeLog  // Kolom N: Diisi Waktu Update (Tanggal & Jam)
                ]]
            ]);

            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                $rangeAudit, 
                $bodyAudit, 
                ['valueInputOption' => 'RAW']
            );

            return redirect()->route('dashboard')->with('success', 'Data administrasi dan log riwayat berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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