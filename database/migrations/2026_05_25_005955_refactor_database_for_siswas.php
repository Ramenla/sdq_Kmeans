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
        // 1. Drop existing cluster_results and sdq_scores to remove foreign key constraints
        Schema::dropIfExists('cluster_results');
        Schema::dropIfExists('sdq_scores');

        // 2. Clean up users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nomor', 'kelas', 'jenis_kelamin', 'tanggal_lahir', 'no_hp']);
        });

        // 3. Create siswas table
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->integer('umur')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable()->unique();
            $table->timestamps();
        });

        // 4. Create skor_sdqs table
        Schema::create('skor_sdqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan')->nullable();
            $table->integer('skor_e')->default(0);
            $table->integer('skor_c')->default(0);
            $table->integer('skor_h')->default(0);
            $table->integer('skor_p')->default(0);
            $table->integer('skor_pr')->default(0);
            $table->integer('skor_diff')->default(0);
            $table->timestamps();
        });

        // 5. Re-create cluster_results with new schema pointing to skor_sdqs
        Schema::create('cluster_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clustering_history_id')->constrained('clustering_histories')->onDelete('cascade');
            $table->foreignId('skor_sdq_id')->constrained('skor_sdqs')->onDelete('cascade');
            $table->integer('cluster_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, down method will not restore the data easily,
        // but we can recreate the schema as it was.
        Schema::dropIfExists('cluster_results');
        Schema::dropIfExists('siswas');

        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
        });

        Schema::create('sdq_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('e_score')->default(0);
            $table->integer('c_score')->default(0);
            $table->integer('h_score')->default(0);
            $table->integer('p_score')->default(0);
            $table->integer('pro_score')->default(0);
            $table->integer('skor_kesulitan')->default(0);
            $table->integer('umur_saat_tes')->nullable();
            $table->timestamps();
        });

        Schema::create('cluster_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clustering_history_id')->constrained('clustering_histories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sdq_score_id')->constrained('sdq_scores')->onDelete('cascade');
            $table->integer('cluster_number');
            $table->timestamps();
        });
    }
};
