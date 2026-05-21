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
    Schema::create('sdq_questions', function (Blueprint $table) {
        $table->id();
        $table->text('question_text');
        $table->enum('category', ['E', 'C', 'H', 'P', 'Prosocial']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdq_questions');
    }
};
