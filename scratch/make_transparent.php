<?php
$sourceFile = 'C:/Users/User/.gemini/antigravity/brain/944fa8b6-3b4a-483c-a756-d8e3e37c83f2/core_asset_logo_white_bg_1778489575672.png';
$outputFile = 'C:/Users/User/.gemini/antigravity/brain/944fa8b6-3b4a-483c-a756-d8e3e37c83f2/core_asset_logo_transparent_final.png';

$data = file_get_contents($sourceFile);
$img = imagecreatefromstring($data);
if (!$img) {
    die("Failed to load image");
}

$width = imagesx($img);
$height = imagesy($img);

// Create new image with transparency
$newImg = imagecreatetruecolor($width, $height);
imagealphablending($newImg, false);
imagesavealpha($newImg, true);
$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);

// Copy original pixels, making white transparent
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $colorIndex = imagecolorat($img, $x, $y);
        $rgba = imagecolorsforindex($img, $colorIndex);
        
        // Check if pixel is white or near-white
        if ($rgba['red'] > 240 && $rgba['green'] > 240 && $rgba['blue'] > 240) {
            // Make transparent
            imagesetpixel($newImg, $x, $y, $transparent);
        } else {
            // Keep original color
            $newColor = imagecolorallocatealpha($newImg, $rgba['red'], $rgba['green'], $rgba['blue'], $rgba['alpha']);
            imagesetpixel($newImg, $x, $y, $newColor);
        }
    }
}

imagepng($newImg, $outputFile);
imagedestroy($img);
imagedestroy($newImg);
echo "Transparent PNG created successfully!";
?>
