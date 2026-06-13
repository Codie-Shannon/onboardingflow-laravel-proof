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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('onboarding_invite_id')
                ->nullable()
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();
    
            $table->string('actor_name')->default('System');
            $table->string('action');
            $table->text('description')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
