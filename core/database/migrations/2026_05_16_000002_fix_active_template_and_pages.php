<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if general_settings exists and update active_template to bit_gold
        if (Schema::hasTable('general_settings')) {
            DB::table('general_settings')
                ->where('active_template', 'basic')
                ->update(['active_template' => 'bit_gold']);
        }

        // Check if pages exists and update tempname to templates.bit_gold.
        if (Schema::hasTable('pages')) {
            DB::table('pages')
                ->where('tempname', 'templates.basic.')
                ->update(['tempname' => 'templates.bit_gold.']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('general_settings')) {
            DB::table('general_settings')
                ->where('active_template', 'bit_gold')
                ->update(['active_template' => 'basic']);
        }

        if (Schema::hasTable('pages')) {
            DB::table('pages')
                ->where('tempname', 'templates.bit_gold.')
                ->update(['tempname' => 'templates.basic.']);
        }
    }
};
