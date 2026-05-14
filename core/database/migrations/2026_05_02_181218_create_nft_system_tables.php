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
        // Table for NFT items
        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->decimal('base_price', 28, 8)->default(0);
            $table->decimal('current_price', 28, 8)->default(0);
            $table->unsignedBigInteger('owner_id')->nullable(); // Current holder
            $table->string('level_id')->nullable(); // Link to user levels/VIP
            $table->enum('status', ['available', 'reserved', 'sold', 'listing'])->default('available');
            $table->timestamps();
        });

        // Table for reservations
        Schema::create('nft_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('nft_id');
            $table->decimal('deposit_amount', 28, 8);
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->timestamps();
        });

        // Table for trading history
        Schema::create('nft_trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nft_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->decimal('buy_price', 28, 8);
            $table->decimal('sell_price', 28, 8)->nullable();
            $table->decimal('profit', 28, 8)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nft_trades');
        Schema::dropIfExists('nft_reservations');
        Schema::dropIfExists('nfts');
    }
};

