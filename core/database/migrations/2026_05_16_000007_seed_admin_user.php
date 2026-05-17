<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if admin exists
        $admin = DB::table('admins')->where('username', 'admin')->first();

        if ($admin) {
            // Update password
            DB::table('admins')->where('username', 'admin')->update([
                'password' => Hash::make('admin')
            ]);
        } else {
            // Insert new admin
            DB::table('admins')->insert([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // nothing
    }
};
