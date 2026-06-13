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
        Schema::create('onboarding_submissions', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();
    
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organisation')->nullable();
            $table->string('role')->nullable();
    
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
    
            $table->text('notes')->nullable();
            $table->json('raw_payload')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_submissions');
    }
};
