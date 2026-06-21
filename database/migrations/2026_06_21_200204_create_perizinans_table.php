<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perizinan', function (Blueprint $table) {
            $table->id();
            $table->string('no_antrian');
            $table->string('nama_pemohon');
            $table->string('no_surat');
            $table->text('deskripsi_surat');
            $table->string('phone');
            $table->text('alamat');
            $table->string('created_by')->nullable();
            $table->date('tgl_pengajuan')->nullable();
            $table->date('tgl_proses')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('status')->default('Dalam Proses');
            $table->string('updated_by')->nullable();
            $table->date('tgl_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};
