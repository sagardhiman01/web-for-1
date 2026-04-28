<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code', 6)->nullable()->after('username');
            });
        }

        DB::table('users')
            ->whereNull('referral_code')
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    do {
                        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    } while (DB::table('users')->where('referral_code', $code)->exists());

                    DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
                }
            });

        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'users')
            ->where('index_name', 'users_referral_code_unique')
            ->exists();

        if (!$indexExists) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('referral_code');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'referral_code')) {
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'users')
                ->where('index_name', 'users_referral_code_unique')
                ->exists();

            if ($indexExists) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique('users_referral_code_unique');
                });
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('referral_code');
            });
        }
    }
};
