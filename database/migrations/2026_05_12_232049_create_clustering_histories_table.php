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
    Schema::create('clustering_histories', function (Blueprint $table) {
        $table->id();
        $table->string('nama_klastering');
        $table->integer('jumlah_k');
        $table->string('filter_kelas')->nullable();
        $table->string('filter_jk')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clustering_histories');
    }
};
