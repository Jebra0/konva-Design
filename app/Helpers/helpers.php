<?php

use App\Models\Template;

// function getFonts(): array
// {
//     // get the font families
//     $fontFiles = [];
//     $directory = public_path('fonts');

//     $files = glob($directory . '/*.ttf');

//     foreach ($files as $file) {
//         $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
//         $parts = explode('-', $filenameWithoutExtension);
//         $cleanedFilename = $parts[0];

//         $fontFiles[] = [
//             'name' => $cleanedFilename,
//             'src' => 'fonts/' . $cleanedFilename . '.ttf'
//         ];
//     }
//     return $fontFiles;
// }

function getTemplates($type)
{
    return Template::whereHas('category', function ($query) use($type) {
        $query->where('name', $type);
    })->select(['id', 'image'])
    ->orderBy('created_at', 'desc')
    ->get();
    
}

function getTemplateImages(): array
{
    $images = [];
    $directory = public_path('images/Templates');

    $files = glob($directory . '/*.png');

    foreach ($files as $file) {
        $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);

        $fontFiles[] = [
            'src' => 'images/Templates/' . $filenameWithoutExtension . '.png'
        ];
    }
    return $fontFiles;
}

// function loadFonts()
// {
//     $cssContent = '';
//     $directory = public_path('fonts');

//     $files = glob($directory . '/*.ttf');

//     foreach ($files as $file) {
//         $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);

//         $parts = explode('-', $filenameWithoutExtension);
//         $cleanedFilename = $parts[0];

//         $cssContent .= "@font-face {
//                 font-family: '$cleanedFilename';
//                 src: url('/fonts/$filenameWithoutExtension.ttf') format('truetype');
//             }\n";
//     }
//     file_put_contents(public_path('css/fonts.css'), $cssContent);

// }

// function pngFont($fontName, $fontFilePath)
// {
//     // $fontName = 'DancingScript';
//     // $fontFilePath = public_path('fonts/DancingScript-VariableFont_wght.ttf');

//     // Create an image with GD
//     $width = 100;
//     $height = 50;
//     $im = imagecreatetruecolor($width, $height);
//     $bgColor = imagecolorallocate($im, 255, 255, 255);
//     imagefill($im, 0, 0, $bgColor);
//     $textColor = imagecolorallocate($im, 0, 0, 0);

//     // Load the font
//     $fontSize = 12;
//     imagettftext($im, $fontSize, 0, 10, 15, $textColor, $fontFilePath, $fontName);

//     // Save the image as PNG
//     $pngPath = 'fonts/' . uniqid() . '.png';
//     $pngFullPath = public_path('images/' . $pngPath);
//     imagepng($im, $pngFullPath);
//     imagedestroy($im);
//     return 'images/'.$pngPath;
// }