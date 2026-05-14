<?php
include 'vendor/autoload.php';
$app = include_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('users', function (Blueprint $table) {
    if (!Schema::hasColumn('users', 'nft_wallet')) {
        $table->decimal('nft_wallet', 28, 8)->default(0);
    }
});

Schema::table('deposits', function (Blueprint $table) {
    if (!Schema::hasColumn('deposits', 'wallet_type')) {
        $table->string('wallet_type', 40)->default('deposit_wallet');
    }
});

echo "Columns added successfully!";
