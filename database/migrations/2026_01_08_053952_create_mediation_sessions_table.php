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
        Schema::create('mediation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique(); // Unique 6-char alphanumeric code
            $table->enum('status', [
                'waiting_for_partner',
                'user1_turn',
                'user2_turn',
                'ai_responding',
                'completed'
            ])->default('waiting_for_partner');
            $table->enum('conflict_type', [
                'relationship',
                'family',
                'roommate',
                'workplace',
                'friendship',
                'other'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediation_sessions');
    }
};
