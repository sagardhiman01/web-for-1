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
        if (!Schema::hasColumn('deposits', 'wallet_type')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('wallet_type')->nullable();
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
        if (Schema::hasColumn('deposits', 'wallet_type')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropColumn('wallet_type');
            });
        }
    }
};
