<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Models\TemplateCategory;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     $texts = getTexts();

//     $shapes = getShapes();

//     $templates = getTemplates();

//     return Inertia::render('design', [
//         'textTemplates' => $texts,
//         'shapeTemplates' => $shapes,
//         'templates' => $templates,
//     ]);
// });

Route::get('/', function () {
    
    $categories = TemplateCategory::select('id', 'name')->get();

    $texts = getTexts();

    $shapes = getShapes();

    $templates = getTemplates();

    $tmplateImages = getTemplateImages();

    $isAdmin = isAdmin();

    return Inertia::render('AdminPanel', [
        'categories'=> $categories,
        'textTemplates' => $texts,
        'shapeTemplates' => $shapes,
        'templates' => $templates,
        'templateImages' => $tmplateImages,
        'isAdmin' => $isAdmin
    ]);
});
// tempaltes
Route::get('/template/{id}/{type}', [TemplateController::class, 'index']);
Route::post('/template/edit/{id}', [TemplateController::class, 'edit']);
Route::delete('/template/delete/{id}/{type}', [TemplateController::class, 'destroy']);
Route::post('/template/picture/add', [TemplateController::class, 'uploadTemplate']);
Route::post('/template/add', [TemplateController::class, 'store']);
Route::post('/template/search', [TemplateController::class, 'search']);
// category
Route::post('/category/add', [TemplateController::class,'addCategory']);
Route::delete('/category/{templateCategory}', [TemplateController::class,'deleteCategory']);

// print 

////////// //////// ////////// ///////// //////// //////////

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
