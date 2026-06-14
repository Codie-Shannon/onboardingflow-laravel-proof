<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            $table->timestamp('email_last_sent_at')->nullable()->after('submitted_at');
            $table->unsignedInteger('email_send_count')->default(0)->after('email_last_sent_at');
            $table->string('email_provider')->nullable()->after('email_send_count');
            $table->text('email_last_error')->nullable()->after('email_provider');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            $table->dropColumn([
                'email_last_sent_at',
                'email_send_count',
                'email_provider',
                'email_last_error',
            ]);
        });
    }
};