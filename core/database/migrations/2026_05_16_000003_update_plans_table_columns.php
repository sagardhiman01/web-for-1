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
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'featured')) {
                    $table->tinyInteger('featured')->default(0)->after('status');
                }
            });

            if (Schema::hasColumn('plans', 'time_setting_id') && !Schema::hasColumn('plans', 'time')) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE plans RENAME COLUMN time_setting_id TO "time"');
            }
            if (Schema::hasColumn('plans', 'times') && !Schema::hasColumn('plans', 'repeat_time')) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE plans RENAME COLUMN times TO repeat_time');
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'featured')) {
                    $table->dropColumn('featured');
                }
            });

            if (Schema::hasColumn('plans', 'time') && !Schema::hasColumn('plans', 'time_setting_id')) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE plans RENAME COLUMN "time" TO time_setting_id');
            }
            if (Schema::hasColumn('plans', 'repeat_time') && !Schema::hasColumn('plans', 'times')) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE plans RENAME COLUMN repeat_time TO times');
            }
        }
    }
};
