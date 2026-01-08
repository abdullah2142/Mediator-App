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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('mediation_sessions')->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('participants')->onDelete('set null');
            $table->text('content');
            $table->enum('type', ['user', 'ai'])->default('user');
            $table->integer('round_number')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
