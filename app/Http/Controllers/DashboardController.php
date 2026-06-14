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
} 