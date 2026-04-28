<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'bep20_wallet_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('bep20_wallet_address', 64)->nullable()->after('referral_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'bep20_wallet_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bep20_wallet_address');
            });
        }
    }
};

