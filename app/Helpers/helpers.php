<?php

use App\Models\Template;

function getFonts(): array
{
    // get the font families
    $fontFiles = [];
    $directory = public_path('fonts');

    $files = glob($directory . '/*.ttf');

    foreach ($files as $file) {
        $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
        $parts = explode('-', $filenameWithoutExtension);
        $cleanedFilename = $parts[0];

        $fontFiles[] = [
            'name' => $cleanedFilename,
            'src' => 'fonts/' . $cleanedFilename . '.ttf'
        ];
    }
    return $fontFiles;
}

function getTemplates($type)
{
    return Template::where('type', $type)
        ->select(['id', 'image'])
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

function loadFonts()
{
    $cssContent = '';
    $directory = public_path('fonts');

    $files = glob($directory . '/*.ttf');

    foreach ($files as $file) {
        $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);

        $parts = explode('-', $filenameWithoutExtension);
        $cleanedFilename = $parts[0];

        $cssContent .= "@font-face {
                font-family: '$cleanedFilename';
                src: url('/fonts/$filenameWithoutExtension.ttf') format('truetype');
            }\n";
    }
    file_put_contents(public_path('css/fonts.css'), $cssContent);

}