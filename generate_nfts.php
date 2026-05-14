<?php
$dir = __DIR__ . '/assets/images/nft/';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

for ($i = 1; $i <= 20; $i++) {
    $width = 800;
    $height = 800;
    $image = imagecreatetruecolor($width, $height);
    
    // Random background color (dark theme)
    $bgR = rand(10, 40);
    $bgG = rand(10, 40);
    $bgB = rand(20, 60);
    $bg = imagecolorallocate($image, $bgR, $bgG, $bgB);
    imagefill($image, 0, 0, $bg);
    
    // Draw 10-20 random shapes
    $shapesCount = rand(10, 25);
    for ($j = 0; $j < $shapesCount; $j++) {
        $color = imagecolorallocatealpha($image, rand(50, 255), rand(50, 255), rand(50, 255), rand(30, 80));
        $shapeType = rand(1, 3);
        
        $x1 = rand(0, $width);
        $y1 = rand(0, $height);
        $size1 = rand(50, 300);
        $size2 = rand(50, 300);
        
        if ($shapeType == 1) {
            imagefilledellipse($image, $x1, $y1, $size1, $size2, $color);
        } elseif ($shapeType == 2) {
            imagefilledrectangle($image, $x1, $y1, $x1 + $size1, $y1 + $size2, $color);
        } else {
            $points = [
                $x1, $y1,
                rand(0, $width), rand(0, $height),
                rand(0, $width), rand(0, $height)
            ];
            imagefilledpolygon($image, $points, 3, $color);
        }
    }
    
    // Add text "NFT #i"
    $textColor = imagecolorallocate($image, 255, 255, 255);
    // Use default built-in font (1-5)
    imagestring($image, 5, $width/2 - 40, $height/2, "CORE NFT #$i", $textColor);
    
    $filename = "unique_nft_$i.jpg";
    imagejpeg($image, $dir . $filename, 90);
    imagedestroy($image);
    echo "Created $filename\n";
}
echo "Done generating 20 NFTs.\n";
