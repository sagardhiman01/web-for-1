<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'firstname')) {
                    $table->string('firstname', 40)->nullable()->after('id');
                }
                if (!Schema::hasColumn('users', 'lastname')) {
                    $table->string('lastname', 40)->nullable()->after('firstname');
                }
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username', 40)->nullable()->after('lastname');
                }
                if (!Schema::hasColumn('users', 'country_code')) {
                    $table->string('country_code', 40)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'mobile')) {
                    $table->string('mobile', 40)->nullable()->after('country_code');
                }
                if (!Schema::hasColumn('users', 'ref_by')) {
                    $table->unsignedBigInteger('ref_by')->default(0)->after('mobile');
                }
                if (!Schema::hasColumn('users', 'deposit_wallet')) {
                    $table->decimal('deposit_wallet', 28, 8)->default(0)->after('ref_by');
                }
                if (!Schema::hasColumn('users', 'interest_wallet')) {
                    $table->decimal('interest_wallet', 28, 8)->default(0)->after('deposit_wallet');
                }
                if (!Schema::hasColumn('users', 'rank')) {
                    $table->integer('rank')->default(0)->after('interest_wallet');
                }
                if (!Schema::hasColumn('users', 'achieved_rewards')) {
                    $table->text('achieved_rewards')->nullable()->after('rank');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->tinyInteger('status')->default(1)->after('achieved_rewards');
                }
                if (!Schema::hasColumn('users', 'ev')) {
                    $table->tinyInteger('ev')->default(0)->after('status');
                }
                if (!Schema::hasColumn('users', 'sv')) {
                    $table->tinyInteger('sv')->default(0)->after('ev');
                }
                if (!Schema::hasColumn('users', 'kv')) {
                    $table->tinyInteger('kv')->default(0)->after('sv');
                }
                if (!Schema::hasColumn('users', 'ver_code')) {
                    $table->string('ver_code', 40)->nullable()->after('kv');
                }
                if (!Schema::hasColumn('users', 'ver_code_send_at')) {
                    $table->timestamp('ver_code_send_at')->nullable()->after('ver_code');
                }
                if (!Schema::hasColumn('users', 'ts')) {
                    $table->tinyInteger('ts')->default(0)->after('ver_code_send_at');
                }
                if (!Schema::hasColumn('users', 'tv')) {
                    $table->tinyInteger('tv')->default(1)->after('ts');
                }
                if (!Schema::hasColumn('users', 'tsc')) {
                    $table->string('tsc', 40)->nullable()->after('tv');
                }
                if (!Schema::hasColumn('users', 'address')) {
                    $table->text('address')->nullable()->after('tsc');
                }
                if (!Schema::hasColumn('users', 'kyc_data')) {
                    $table->text('kyc_data')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'referral_code')) {
                    $table->string('referral_code', 6)->nullable()->after('kyc_data');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to drop columns in rollback for this fix
    }
};
