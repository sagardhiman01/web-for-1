<?php
require 'core/vendor/autoload.php';
require 'core/bootstrap/app.php';
$nfts = App\Models\Nft::all();
foreach($nfts as $nft) {
    echo $nft->name . ' - ' . $nft->image . PHP_EOL;
}
$pool = (new App\Http\Controllers\User\NftController)->getNftImagePool(); // private method, so we can't call it directly.

$imageDir = realpath(base_path('../assets/images/nft')) ?: base_path('../assets/images/nft');
echo "Image Dir: " . $imageDir . PHP_EOL;
echo "Is Dir? " . (is_dir($imageDir) ? 'Yes' : 'No') . PHP_EOL;
$files = is_dir($imageDir) ? scandir($imageDir) : [];
$images = array_filter($files, function($f) { return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'webp']); });
echo "Count images: " . count($images) . PHP_EOL;
