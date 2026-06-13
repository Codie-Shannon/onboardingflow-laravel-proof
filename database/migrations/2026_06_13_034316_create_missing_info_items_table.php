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
        Schema::create('missing_info_items', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();
    
            $table->foreignId('onboarding_submission_id')
                ->nullable()
                ->constrained('onboarding_submissions')
                ->nullOnDelete();
    
            $table->string('field_key');
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('severity')->default('warning');
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_info_items');
    }
};
