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
        Schema::create('onboarding_notes', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();
    
            $table->string('author_name')->default('Admin User');
            $table->text('note');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_notes');
    }
};
