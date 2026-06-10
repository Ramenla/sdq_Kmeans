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
        Schema::table('skor_sdqs', function (Blueprint $table) {
            // Kolom utama Forward Chaining: Normal / Borderline / Abnormal
            $table->string('kategori', 20)->nullable()->after('skor_diff');

            // Kolom sub-indikator per gejala
            $table->string('kategori_e', 20)->nullable()->after('kategori');
            $table->string('kategori_c', 20)->nullable()->after('kategori_e');
            $table->string('kategori_h', 20)->nullable()->after('kategori_c');
            $table->string('kategori_p', 20)->nullable()->after('kategori_h');
            $table->string('kategori_pr', 20)->nullable()->after('kategori_p');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skor_sdqs', function (Blueprint $table) {
            $table->dropColumn([
                'kategori',
                'kategori_e',
                'kategori_c',
                'kategori_h',
                'kategori_p',
                'kategori_pr',
            ]);
        });
    }
};
