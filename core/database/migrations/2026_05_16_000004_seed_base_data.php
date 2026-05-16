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
        // Seed Time Settings
        if (Schema::hasTable('time_settings')) {
            $times = [
                ['name' => 'Hour', 'time' => '1'],
                ['name' => 'Day', 'time' => '24'],
                ['name' => 'Week', 'time' => '168'],
                ['name' => 'Month', 'time' => '720'],
            ];

            foreach ($times as $time) {
                if (DB::table('time_settings')->where('time', $time['time'])->count() == 0) {
                    DB::table('time_settings')->insert(array_merge($time, ['created_at' => now(), 'updated_at' => now()]));
                }
            }
        }

        // Seed Plans
        if (Schema::hasTable('plans')) {
            $plans = [
                [
                    'name' => 'Starter Plan',
                    'minimum' => 10,
                    'maximum' => 100,
                    'fixed_amount' => 0,
                    'interest' => 1,
                    'interest_type' => '1', // Percentage
                    'time' => '1', // Every Hour
                    'lifetime' => '0',
                    'repeat_time' => 24,
                    'capital_back' => 1,
                    'status' => 1,
                    'featured' => 1
                ],
                [
                    'name' => 'Silver Plan',
                    'minimum' => 100,
                    'maximum' => 1000,
                    'fixed_amount' => 0,
                    'interest' => 2,
                    'interest_type' => '1',
                    'time' => '24', // Every Day
                    'lifetime' => '0',
                    'repeat_time' => 30,
                    'capital_back' => 1,
                    'status' => 1,
                    'featured' => 1
                ],
                [
                    'name' => 'Gold Plan',
                    'minimum' => 1000,
                    'maximum' => 10000,
                    'fixed_amount' => 0,
                    'interest' => 5,
                    'interest_type' => '1',
                    'time' => '24', // Every Day
                    'lifetime' => '1',
                    'repeat_time' => 0,
                    'capital_back' => 0,
                    'status' => 1,
                    'featured' => 1
                ],
                [
                    'name' => 'Fixed 500',
                    'minimum' => 0,
                    'maximum' => 0,
                    'fixed_amount' => 500,
                    'interest' => 10,
                    'interest_type' => '1',
                    'time' => '168', // Every Week
                    'lifetime' => '0',
                    'repeat_time' => 52,
                    'capital_back' => 1,
                    'status' => 1,
                    'featured' => 0
                ]
            ];

            foreach ($plans as $plan) {
                if (DB::table('plans')->where('name', $plan['name'])->count() == 0) {
                    DB::table('plans')->insert(array_merge($plan, ['created_at' => now(), 'updated_at' => now()]));
                }
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
        // No need to remove data on rollback for this specific seeder
    }
};
