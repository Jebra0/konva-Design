<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddFonts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-fonts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish the new fonts u added to public directory';

    /**
     * Execute the console command.
     */
    public function handle()
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

        $this->info('Font CSS generated successfully.');

        return 0;
    }
}
