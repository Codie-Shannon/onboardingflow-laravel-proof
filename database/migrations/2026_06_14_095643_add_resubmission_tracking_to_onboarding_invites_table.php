<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            if (! Schema::hasColumn('onboarding_invites', 'resubmitted_at')) {
                $table->timestamp('resubmitted_at')->nullable()->after('submitted_at');
            }

            if (! Schema::hasColumn('onboarding_invites', 'resubmission_count')) {
                $table->unsignedInteger('resubmission_count')->default(0)->after('resubmitted_at');
            }

            if (! Schema::hasColumn('onboarding_invites', 'last_follow_up_sent_at')) {
                $table->timestamp('last_follow_up_sent_at')->nullable()->after('email_last_error');
            }

            if (! Schema::hasColumn('onboarding_invites', 'follow_up_send_count')) {
                $table->unsignedInteger('follow_up_send_count')->default(0)->after('last_follow_up_sent_at');
            }

            if (! Schema::hasColumn('onboarding_invites', 'follow_up_last_error')) {
                $table->text('follow_up_last_error')->nullable()->after('follow_up_send_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_invites', function (Blueprint $table) {
            $columns = [
                'resubmitted_at',
                'resubmission_count',
                'last_follow_up_sent_at',
                'follow_up_send_count',
                'follow_up_last_error',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('onboarding_invites', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};