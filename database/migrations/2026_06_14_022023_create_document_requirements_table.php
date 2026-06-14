<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();

            $table->string('label');
            $table->text('description')->nullable();
            $table->string('status')->default('missing');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('reviewed_at')->nullable();
            $table->string('reviewed_by')->nullable();

            $table->timestamps();

            $table->index(['onboarding_invite_id', 'sort_order']);
            $table->index(['onboarding_invite_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};