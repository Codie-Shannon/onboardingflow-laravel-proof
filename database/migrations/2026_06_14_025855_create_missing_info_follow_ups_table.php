<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missing_info_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('onboarding_invite_id')
                ->constrained('onboarding_invites')
                ->cascadeOnDelete();

            $table->foreignId('missing_info_item_id')
                ->nullable()
                ->constrained('missing_info_items')
                ->nullOnDelete();

            $table->text('message');
            $table->string('status')->default('open');

            $table->string('requested_by')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('due_at')->nullable();

            $table->string('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['onboarding_invite_id', 'status']);
            $table->index(['missing_info_item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missing_info_follow_ups');
    }
};