<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perizinan extends Model
{
    protected $table = 'perizinan'; 

    protected $fillable = [
        'no_antrian',
        'nama_pemohon',
        'no_surat',
        'deskripsi_surat',
        'phone',
        'alamat',
        'created_by',
        'tgl_pengajuan',
        'tgl_proses',
        'tgl_selesai',
        'status',
        'updated_by',
        'tgl_update'
    ];
}
