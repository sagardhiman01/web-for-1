<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Nft;

Nft::truncate();

$nfts = [
    ['name' => 'Cyber Ape #001', 'price' => 50, 'level' => 1],
    ['name' => 'Cyber Ape #042', 'price' => 75, 'level' => 1],
    ['name' => 'Neon Punk #102', 'price' => 100, 'level' => 1],
    ['name' => 'Neon Punk #255', 'price' => 150, 'level' => 1],
    ['name' => 'Meta Samurai #08', 'price' => 200, 'level' => 2],
    ['name' => 'Meta Samurai #19', 'price' => 250, 'level' => 2],
    ['name' => 'Treasure Genesis #404', 'price' => 300, 'level' => 2],
    ['name' => 'Treasure Genesis #505', 'price' => 400, 'level' => 2],
    ['name' => 'Galactic Dragon #01', 'price' => 500, 'level' => 3],
    ['name' => 'Galactic Dragon #09', 'price' => 650, 'level' => 3],
    ['name' => 'Crystal Golem #11', 'price' => 800, 'level' => 3],
    ['name' => 'Crystal Golem #22', 'price' => 1000, 'level' => 4],
    ['name' => 'Void Walker #007', 'price' => 1200, 'level' => 4],
    ['name' => 'Void Walker #008', 'price' => 1500, 'level' => 4],
    ['name' => 'Aether Knight #10', 'price' => 2000, 'level' => 5],
    ['name' => 'Aether Knight #20', 'price' => 2500, 'level' => 5],
    ['name' => 'Quantum Beast #99', 'price' => 3000, 'level' => 5],
    ['name' => 'Quantum Beast #100', 'price' => 4000, 'level' => 6],
    ['name' => 'Supreme Overlord #1', 'price' => 5000, 'level' => 6],
    ['name' => 'Supreme Overlord #2', 'price' => 10000, 'level' => 7],
];

foreach ($nfts as $index => $nftData) {
    $imgNum = ($index % 2) + 1; // Alternates between nft_1.png and nft_2.png
    Nft::create([
        'name' => $nftData['name'],
        'image' => 'nft_' . $imgNum . '.png',
        'base_price' => $nftData['price'],
        'current_price' => $nftData['price'],
        'level_id' => $nftData['level'],
        'status' => 'available'
    ]);
}

echo "20 NFTs Seeded Successfully!";
