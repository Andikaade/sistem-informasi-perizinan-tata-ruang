<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon; // 1. PERBAIKAN: Pindahan ke atas luar class
use Google\Client;
use Google\Service\Sheets;

#[Signature('app:auto-delete-old-perizinan')]
#[Description('Menghapus otomatis data perizinan yang sudah berusia lebih dari 1 tahun dari tanggal pengajuan')]
class AutoDeleteOldPerizinan extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses pembersihan data perizinan lama...');

        try {
            // Inisialisasi Google Service
            $service = $this->getGoogleSheetsService();
            $spreadsheetId = env('1zY1TCWEoHDW24uWVm7fQ-i07QySyBPmJzno6CE7mOUs'); // Ambil ID dari file .env
            $range = 'Sheet1!A:K'; // Sesuai kolom spreadsheet Anda (A sampai K)

            // 1. Ambil semua data dari Google Sheets
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();

            if (empty($rows)) {
                $this->warn('Tidak ada data ditemukan di Google Sheets.');
                return Command::SUCCESS;
            }

            $updatedRows = [];
            $deletedCount = 0;

            foreach ($rows as $index => $row) {
                // Pertahankan baris header (Baris ke-1)
                if ($index === 0) {
                    $updatedRows[] = $row;
                    continue;
                }

                // Ambil kolom tgl_pengajuan (Kolom H / Indeks ke-7 sesuai spreadsheet)
                // Pastikan format di Google Sheets Anda adalah YYYY-MM-DD atau format tanggal standar
                $tglPengajuanRaw = $row[7] ?? null;

                if (empty($tglPengajuanRaw) || $tglPengajuanRaw === '-') {
                    // Jika tanggal kosong, tetap pertahankan datanya demi keamanan
                    $updatedRows[] = $row;
                    continue;
                }

                try {
                    $tglPengajuan = Carbon::parse($tglPengajuanRaw);

                    // Jika usia data masih di bawah 1 tahun, pertahankan datanya
                    if ($tglPengajuan->diffInYears(Carbon::now()) < 1) {
                        $updatedRows[] = $row;
                    } else {
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    // Jika gagal parsing tanggal karena format rusak, amankan data (jangan dihapus)
                    $updatedRows[] = $row;
                }
            }

            // 2. Jika ada data yang harus dihapus, timpa isi Google Sheets dengan data baru
            if ($deletedCount > 0) {
                // Kosongkan sheet terlebih dahulu sebelum diisi data baru yang sudah bersih
                $service->spreadsheets_values->clear($spreadsheetId, $range, new \Google\Service\Sheets\ClearValuesRequest());

                // Masukkan data ter-update
                $body = new \Google\Service\Sheets\ValueRange([
                    'values' => $updatedRows
                ]);
                $params = ['valueInputOption' => 'RAW'];

                $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
                $this->info("Sukses menghapus {$deletedCount} data perizinan yang sudah usang.");
            } else {
                $this->info('Tidak ada data yang berusia lebih dari 1 tahun. Semua data aman.');
            }

        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Helper Service untuk Inisialisasi Google Sheets API
     */
    private function getGoogleSheetsService()
    {
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        return new \Google\Service\Sheets($client);
    }
}