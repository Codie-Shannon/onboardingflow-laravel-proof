<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_checklist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();

            $table->string('label');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('completed_by')->nullable();

            $table->timestamps();

            $table->index(['onboarding_invite_id', 'sort_order']);
            $table->index(['onboarding_invite_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_checklist_items');
    }
};