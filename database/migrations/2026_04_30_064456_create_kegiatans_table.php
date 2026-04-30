<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kegiatan');
            $table->string('moda');
            $table->string('fasil')->nullable();
            $table->integer('kuota')->default(0);
            $table->dateTime('waktu_pelaksanaan');
            $table->string('nama_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->string('link_zoom')->nullable();
            $table->string('flayer')->nullable();
            $table->string('slug')->unique();
            $table->string('link_pendaftaran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};