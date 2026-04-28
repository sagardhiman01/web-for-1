<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_investment_codes')) {
            return;
        }

        Schema::create('user_investment_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_code_id');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'investment_code_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('investment_code_id')->references('id')->on('investment_codes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_investment_codes');
    }
};

