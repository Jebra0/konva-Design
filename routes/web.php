<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Models\Template;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});
////////////////////////////// TEST ////////////////////////

Route::get('/design', function () {
    return Inertia::render('Design');
});
Route::get('/test', function () {
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
    // templates
    $temps = Template::where('type', 'Text')
        ->select(['id', 'image'])
        ->orderBy('created_at', 'desc')
        ->get();

    $shapes = Template::where('type', 'Shapes')
        ->select(['id', 'image'])
        ->orderBy('created_at', 'desc')
        ->get();

    return Inertia::render('Test', [
        'fonts' => $fontFiles, 
        'textTemplates' => $temps,
        'shapeTemplates'=> $shapes
    ]);
});

Route::get('/template/{template}', [TemplateController::class, 'index']);
Route::post('/template/add', [TemplateController::class, 'store']);
Route::post('/template/picture/add', [TemplateController::class, 'uploadTemplate']);

////////////////////////////////////////////////////////

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
