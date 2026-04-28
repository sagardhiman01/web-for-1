<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('investment_codes')) {
            return;
        }

        Schema::table('investment_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('investment_codes', 'max_users')) {
                $table->unsignedInteger('max_users')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('investment_codes', 'valid_hours')) {
                $table->unsignedInteger('valid_hours')->default(24)->after('max_users');
            }

            if (!Schema::hasColumn('investment_codes', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('valid_hours');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('investment_codes')) {
            return;
        }

        Schema::table('investment_codes', function (Blueprint $table) {
            if (Schema::hasColumn('investment_codes', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('investment_codes', 'valid_hours')) {
                $table->dropColumn('valid_hours');
            }
            if (Schema::hasColumn('investment_codes', 'max_users')) {
                $table->dropColumn('max_users');
            }
        });
    }
};

