<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'bep20_wallet_address')) {
            return;
        }

        // Normalize current values before applying unique constraint.
        DB::table('users')
            ->whereNotNull('bep20_wallet_address')
            ->update([
                'bep20_wallet_address' => DB::raw("LOWER(TRIM(bep20_wallet_address))"),
            ]);

        DB::table('users')
            ->whereRaw("bep20_wallet_address = ''")
            ->update(['bep20_wallet_address' => null]);

        $duplicateCount = DB::table('users')
            ->selectRaw('LOWER(bep20_wallet_address) as wallet')
            ->whereNotNull('bep20_wallet_address')
            ->groupBy('wallet')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        if ($duplicateCount > 0) {
            throw new RuntimeException('Duplicate BEP20 wallet addresses found. Resolve duplicates before running this migration.');
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('bep20_wallet_address', 'users_bep20_wallet_address_unique');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'bep20_wallet_address')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_bep20_wallet_address_unique');
            });
        } catch (\Throwable $e) {}
    }
};

