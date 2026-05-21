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
    Schema::create('sdq_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->integer('e_score')->default(0);
        $table->integer('c_score')->default(0);
        $table->integer('h_score')->default(0);
        $table->integer('p_score')->default(0);
        $table->integer('pro_score')->default(0);
        
        $table->integer('skor_kesulitan')->default(0);
        $table->integer('umur_saat_tes');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdq_scores');
    }
};
