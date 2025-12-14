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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('photo');
            $table->text('about');
            $table->integer('yoe'); //years of experience
            $table->foreignId('specialist_id')->constrained()->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->string('gender');
            $table->timestamps();
            $table->softDeletes();

            // $table->softDeletes();
            // data softdeletes adalah teknik dalam menghapus data tetapi secara data yang
            // dihapus tersebut masih tersimpan di database, hanya saja tidak ditampilkan pada
            // query biasa. Biasanya teknik ini digunakan untuk menghindari kehilangan data yang
            // penting secara permanen. DAN SEBAGIAN DATA ITU TIDAK HARUS 100% DIHAPUS, KARENA ADA BEBERAPA
            // DATA YANG MASIH PERLU DISIMPAN.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
