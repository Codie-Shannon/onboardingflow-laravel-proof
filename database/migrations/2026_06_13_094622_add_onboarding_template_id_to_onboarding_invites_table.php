<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            $table->foreignId('onboarding_template_id')
                ->nullable()
                ->after('id')
                ->constrained('onboarding_templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('onboarding_template_id');
        });
    }
};