<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('nfts', 'last_profit_at')) {
    Schema::table('nfts', function (Blueprint $table) {
        $table->timestamp('last_profit_at')->nullable();
    });
    echo "Column last_profit_at added to nfts table\n";
} else {
    echo "Column last_profit_at already exists\n";
}
