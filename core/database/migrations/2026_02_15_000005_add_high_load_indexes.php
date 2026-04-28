<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('deposits', 'idx_dep_user_status', ['user_id', 'status']);
        $this->addIndexIfMissing('deposits', 'idx_dep_user_method_status', ['user_id', 'method_code', 'status']);
        $this->addIndexIfMissing('deposits', 'idx_dep_user_created', ['user_id', 'created_at']);
        $this->addIndexIfMissing('deposits', 'idx_dep_user_trx', ['user_id', 'trx']);

        $this->addIndexIfMissing('withdrawals', 'idx_wdr_user_status', ['user_id', 'status']);
        $this->addIndexIfMissing('withdrawals', 'idx_wdr_user_created', ['user_id', 'created_at']);
        $this->addIndexIfMissing('withdrawals', 'idx_wdr_user_trx', ['user_id', 'trx']);

        $this->addIndexIfMissing('transactions', 'idx_trx_user_created', ['user_id', 'created_at']);
        $this->addIndexIfMissing('transactions', 'idx_trx_user_remark', ['user_id', 'remark']);
        $this->addIndexIfMissing('transactions', 'idx_trx_user_wallet', ['user_id', 'wallet_type']);
        $this->addIndexIfMissing('transactions', 'idx_trx_user_trx', ['user_id', 'trx']);

        $this->addIndexIfMissing('invests', 'idx_inv_user_status', ['user_id', 'status']);
        $this->addIndexIfMissing('invests', 'idx_inv_user_wallet_status', ['user_id', 'wallet_type', 'status']);
        $this->addIndexIfMissing('invests', 'idx_inv_user_plan_status', ['user_id', 'plan_id', 'status']);
        $this->addIndexIfMissing('invests', 'idx_inv_user_created', ['user_id', 'created_at']);

        $this->addIndexIfMissing('support_tickets', 'idx_tkt_user_status', ['user_id', 'status']);
        $this->addIndexIfMissing('support_tickets', 'idx_tkt_user_created', ['user_id', 'created_at']);

        $this->addIndexIfMissing('user_investment_codes', 'idx_uic_user_redeemed', ['user_id', 'redeemed_at']);
        $this->addIndexIfMissing('user_investment_codes', 'idx_uic_code_redeemed', ['investment_code_id', 'redeemed_at']);
        $this->addIndexIfMissing('investment_codes', 'idx_ic_status_expires', ['status', 'expires_at']);
    }

    public function down(): void
    {
        $this->dropIndexIfExists('deposits', 'idx_dep_user_status');
        $this->dropIndexIfExists('deposits', 'idx_dep_user_method_status');
        $this->dropIndexIfExists('deposits', 'idx_dep_user_created');
        $this->dropIndexIfExists('deposits', 'idx_dep_user_trx');

        $this->dropIndexIfExists('withdrawals', 'idx_wdr_user_status');
        $this->dropIndexIfExists('withdrawals', 'idx_wdr_user_created');
        $this->dropIndexIfExists('withdrawals', 'idx_wdr_user_trx');

        $this->dropIndexIfExists('transactions', 'idx_trx_user_created');
        $this->dropIndexIfExists('transactions', 'idx_trx_user_remark');
        $this->dropIndexIfExists('transactions', 'idx_trx_user_wallet');
        $this->dropIndexIfExists('transactions', 'idx_trx_user_trx');

        $this->dropIndexIfExists('invests', 'idx_inv_user_status');
        $this->dropIndexIfExists('invests', 'idx_inv_user_wallet_status');
        $this->dropIndexIfExists('invests', 'idx_inv_user_plan_status');
        $this->dropIndexIfExists('invests', 'idx_inv_user_created');

        $this->dropIndexIfExists('support_tickets', 'idx_tkt_user_status');
        $this->dropIndexIfExists('support_tickets', 'idx_tkt_user_created');

        $this->dropIndexIfExists('user_investment_codes', 'idx_uic_user_redeemed');
        $this->dropIndexIfExists('user_investment_codes', 'idx_uic_code_redeemed');
        $this->dropIndexIfExists('investment_codes', 'idx_ic_status_expires');
    }

    private function addIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }

        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
            $tableBlueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
            $tableBlueprint->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver !== 'mysql') {
            return false;
        }

        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        return !empty($result);
    }
};

