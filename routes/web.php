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
////////////////////////////// my routes ////////////////////////

Route::get('/Arabian-Geeks', function () {
    $fontFiles = getFonts();

    $temps = getTemplates('Text');

    $shapes = getTemplates('Shapes');

    $templates = getTemplates('Fold brochure');

    return Inertia::render('index', [
        'fonts' => $fontFiles,
        'textTemplates' => $temps,
        'shapeTemplates'=> $shapes,
        'templates'=> $templates,
    ]);
});

Route::get('/design', function () {
    $fontFiles = getFonts();

    $temps = getTemplates('Text');

    $shapes = getTemplates('Shapes');

    $templates = getTemplates('Fold brochure');

    return Inertia::render('Design', [
        'fonts' => $fontFiles,
        'textTemplates' => $temps,
        'shapeTemplates'=> $shapes,
        'templates'=> $templates,
    ]);
});
Route::get('/test', function () {
    $fontFiles = getFonts();

    $temps = getTemplates('Text');

    $shapes = getTemplates('Shapes');

    $templates = getTemplates('Fold brochure');

    return Inertia::render('Test', [
        'fonts' => $fontFiles,
        'textTemplates' => $temps,
        'shapeTemplates'=> $shapes,
        'templates'=> $templates,
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
