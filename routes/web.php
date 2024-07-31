<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
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

    $temps = getTemplates('Text');

    $shapes = getTemplates('Shapes');

    $templates = getTemplates('Fold brochre');

    return Inertia::render('index', [
        'textTemplates' => $temps,
        'shapeTemplates' => $shapes,
        'templates' => $templates,
    ]);
});

Route::get('/admin-panel', function () {

    $temps = getTemplates('Text');

    $shapes = getTemplates('Shapes');

    $templates = getTemplates('Fold brochre');

    $tmplateImages = getTemplateImages();

    return Inertia::render('AdminPanel', [
        'textTemplates' => $temps,
        'shapeTemplates' => $shapes,
        'templates' => $templates,
        'templateImages' => $tmplateImages,
    ]);
});

Route::get('/template/{template}', [TemplateController::class, 'index']);
Route::post('/template/add', [TemplateController::class, 'store']);
Route::post('/template/picture/add', [TemplateController::class, 'uploadTemplate']);
// Route::post('/font/add', [TemplateController::class,'addFont']);
Route::post('/category/add', [TemplateController::class,'addCategory']);

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
