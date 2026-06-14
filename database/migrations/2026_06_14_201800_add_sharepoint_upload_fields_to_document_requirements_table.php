<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->string('sharepoint_drive_id')->nullable()->after('reviewed_by');
            $table->string('sharepoint_item_id')->nullable()->after('sharepoint_drive_id');
            $table->text('sharepoint_web_url')->nullable()->after('sharepoint_item_id');
            $table->string('uploaded_original_name')->nullable()->after('sharepoint_web_url');
            $table->string('uploaded_mime_type')->nullable()->after('uploaded_original_name');
            $table->unsignedBigInteger('uploaded_size')->nullable()->after('uploaded_mime_type');
            $table->timestamp('uploaded_at')->nullable()->after('uploaded_size');
            $table->text('upload_error')->nullable()->after('uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropColumn([
                'sharepoint_drive_id',
                'sharepoint_item_id',
                'sharepoint_web_url',
                'uploaded_original_name',
                'uploaded_mime_type',
                'uploaded_size',
                'uploaded_at',
                'upload_error',
            ]);
        });
    }
};
