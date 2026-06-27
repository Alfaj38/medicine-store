<?php

function removeBackground($inFile, $outFile) {
    if (!file_exists($inFile)) {
        echo "File not found: $inFile\n";
        return;
    }
    
    $img = imagecreatefromstring(file_get_contents($inFile));
    if (!$img) {
        echo "Could not load image: $inFile\n";
        return;
    }
    
    $width = imagesx($img);
    $height = imagesy($img);

    $outImg = imagecreatetruecolor($width, $height);
    imagesavealpha($outImg, true);
    $transColour = imagecolorallocatealpha($outImg, 0, 0, 0, 127);
    imagefill($outImg, 0, 0, $transColour);
    
    // Assume top-left pixel is the background color (should be white now)
    $bg = imagecolorat($img, 0, 0);
    $bgR = ($bg >> 16) & 0xFF;
    $bgG = ($bg >> 8) & 0xFF;
    $bgB = $bg & 0xFF;

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $color = imagecolorat($img, $x, $y);
            $r = ($color >> 16) & 0xFF;
            $g = ($color >> 8) & 0xFF;
            $b = $color & 0xFF;
            
            // Calculate distance to background color
            $dist = sqrt(pow($r - $bgR, 2) + pow($g - $bgG, 2) + pow($b - $bgB, 2));
            
            if ($dist < 35) {
                // Background -> transparent
                imagesetpixel($outImg, $x, $y, $transColour);
            } else if ($dist < 80) {
                // Anti-aliasing fringe -> semi-transparent
                $alpha = (int)(127 * (1 - ($dist - 35) / 45));
                $newColor = imagecolorallocatealpha($outImg, $r, $g, $b, $alpha);
                imagesetpixel($outImg, $x, $y, $newColor);
            } else {
                imagesetpixel($outImg, $x, $y, $color);
            }
        }
    }
    
    imagepng($outImg, $outFile);
    imagedestroy($img);
    imagedestroy($outImg);
    echo "Processed $inFile -> $outFile\n";
}

removeBackground("C:\\Users\\ROWTECH\\.gemini\\antigravity\\brain\\d2d013a9-e09b-46e4-ba6a-a86b842af135\\shield_white_1782576300407.png", __DIR__ . "/public/images/shield_3d.png");
removeBackground("C:\\Users\\ROWTECH\\.gemini\\antigravity\\brain\\d2d013a9-e09b-46e4-ba6a-a86b842af135\\pill_white_1782576310420.png", __DIR__ . "/public/images/pill_3d.png");
removeBackground("C:\\Users\\ROWTECH\\.gemini\\antigravity\\brain\\d2d013a9-e09b-46e4-ba6a-a86b842af135\\bottle_white_1782576320664.png", __DIR__ . "/public/images/bottle_3d.png");

?>
