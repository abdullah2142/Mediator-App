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
        Schema::create('user_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('mediation_sessions')->cascadeOnDelete();
            $table->enum('save_level', ['all', 'summary']);
            $table->string('partner_name')->nullable();
            $table->string('conflict_type');
            $table->text('transcript')->nullable(); // Only if save_level = 'all'
            $table->text('summary');
            $table->json('insights')->nullable(); // Extracted themes, patterns
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'conflict_type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memories');
    }
};
