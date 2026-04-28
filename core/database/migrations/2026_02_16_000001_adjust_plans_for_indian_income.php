<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        $timeName = DB::table('time_settings')->where('time', 24)->value('name') ?: 'Day';
        $now = now();

        $targetPlans = [
            [
                'name' => 'Starter Plan',
                'fixed_amount' => 15,
                'interest' => 1.00,
            ],
            [
                'name' => 'Basic Plan',
                'fixed_amount' => 35,
                'interest' => 1.10,
            ],
            [
                'name' => 'Smart Plan',
                'fixed_amount' => 75,
                'interest' => 1.20,
            ],
            [
                'name' => 'Growth Plan',
                'fixed_amount' => 150,
                'interest' => 1.30,
            ],
            [
                'name' => 'Premium Plan',
                'fixed_amount' => 300,
                'interest' => 1.40,
            ],
            [
                'name' => 'Elite Plan',
                'fixed_amount' => 600,
                'interest' => 1.50,
            ],
        ];

        $existingIds = DB::table('plans')->orderBy('id')->pluck('id')->all();
        $targetPlanIds = [];

        foreach ($targetPlans as $index => $planData) {
            $payload = [
                'name' => $planData['name'],
                'minimum' => 0,
                'maximum' => 0,
                'fixed_amount' => $planData['fixed_amount'],
                'interest' => $planData['interest'],
                'interest_type' => 1,
                'time' => 24,
                'time_name' => $timeName,
                'status' => 1,
                'featured' => 1,
                'capital_back' => 1,
                'lifetime' => 0,
                'repeat_time' => 30,
                'updated_at' => $now,
            ];

            if (isset($existingIds[$index])) {
                DB::table('plans')->where('id', $existingIds[$index])->update($payload);
                $targetPlanIds[] = $existingIds[$index];
                continue;
            }

            $targetPlanIds[] = DB::table('plans')->insertGetId(array_merge($payload, [
                'created_at' => $now,
            ]));
        }

        if (!empty($targetPlanIds)) {
            DB::table('plans')
                ->whereNotIn('id', $targetPlanIds)
                ->update([
                    'featured' => 0,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Not reversible safely because existing plan rows are updated in place.
    }
};
