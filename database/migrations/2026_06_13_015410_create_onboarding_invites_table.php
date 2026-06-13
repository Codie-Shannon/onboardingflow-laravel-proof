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
        Schema::create('onboarding_invites', function (Blueprint $table) {
            $table->id();
    
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->string('role')->nullable();
            $table->string('organisation')->nullable();
    
            $table->string('token')->unique();
            $table->string('status')->default('sent');
    
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
    
            $table->text('message')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_invites');
    }
};
