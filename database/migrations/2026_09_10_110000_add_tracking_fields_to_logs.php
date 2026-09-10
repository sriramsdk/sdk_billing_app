<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });

        Schema::table('mail_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('mail_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('mail_logs', 'message_id')) {
                $table->string('message_id')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            if (Schema::hasColumn('mail_logs', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('mail_logs', 'message_id')) {
                $table->dropColumn('message_id');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
};