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
        Schema::create('onboarding_templates', function (Blueprint $table) {
            $table->id();
    
            $table->string('name');
            $table->text('description')->nullable();
    
            $table->unsignedInteger('default_expiry_days')->default(7);
    
            $table->json('required_fields')->nullable();
            $table->json('required_documents')->nullable();
            $table->json('review_checklist')->nullable();
    
            $table->boolean('is_active')->default(true);
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_templates');
    }
};
