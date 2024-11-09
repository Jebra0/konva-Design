<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Models\Design;
use App\Models\TemplateCategory;
use App\Repositories\Cart\CartModelRepository;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {

    $categories = TemplateCategory::select('id', 'name')->get();

    $texts = getTexts();

    $shapes = getShapes();

    $templates = getTemplates();

    $tmplateImages = getImages();

    $isAdmin = isAdmin();

    $user = Auth::user();

    $repo = new CartModelRepository();
    $cart = $repo->get();

    $my_designs = '';
    $user_images = '';

    if ($user) {
        $my_designs = $user->designs()
            ->orderBy('created_at', 'desc')
            ->get();

        $user_images = $user->images()->orderBy('created_at', 'desc')
            ->get();
    }

    return Inertia::render('DesignPage', [
        'categories' => $categories,
        'textTemplates' => $texts,
        'shapeTemplates' => $shapes,
        'templates' => $templates,
        'templateImages' => $tmplateImages,
        'isAdmin' => $isAdmin,
        'user' => $user,
        'my_designs' => $my_designs,
        'user_images' => $user_images,
        'items' => $cart->count(),

    ]);
})->name('home');

// tempaltes
Route::group([
    'prefix' => "template",
    'as' => "template."
], function () {
    Route::group([
        'middleware' => "auth",
    ], function () {
        Route::post('/edit/{id}', [TemplateController::class, 'edit'])
            ->name('edit');

        Route::delete('/delete/{id}', [TemplateController::class, 'destroy'])
            ->name('delete');


        Route::post('/add', [TemplateController::class, 'store'])
            ->name('add');

        Route::post('/picture/add', [TemplateController::class, 'uploadTemplate'])
            ->name('addImage');

        Route::delete('/picture/delete/{id}', [TemplateController::class, 'deleteImage'])
            ->name('deleteImage');

        Route::delete('/picture/delete', [TemplateController::class, 'deleteAdminImages'])
            ->name('deleteAdminImages');
    });

    Route::get('/{id}/{type}', [TemplateController::class, 'getTemplate'])
        ->name('get');

    Route::post('/search', [TemplateController::class, 'search'])
        ->name('search');
});

// cart
Route::group([
    'prefix' => "cart",
    'as' => "cart."
], function () {
    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/', [CartController::class, 'store'])
        ->name('add');

    Route::delete('/delete/{cart}', [CartController::class, 'destroy'])
        ->name('delete');

    Route::post('/cart/update/{cart}', [CartController::class, 'update'])
        ->name('update');
});

//checkout
Route::group([
    'prefix' => "checkout",
    'middleware' => "auth",
    'as' => "checkout."
], function () {

    Route::get('/', [CheckoutController::class, 'index'])
        ->name('index');

    Route::post('/', [CheckoutController::class, 'store'])
        ->name('store');
});

// Admin Dashboard
Route::group(['middleware' => "auth"], function () {
    Route::group([
        'prefix' => "admin/dashboard",
        'middleware' => "is_admin",
        'as' => "admin."
    ], function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('index');

        Route::resource('/product', CategoryController::class);
        Route::get('products/search', [CategoryController::class, 'search'])
            ->name('product.search');

        Route::resource('/orders', OrderController::class);

        Route::post('/order/status-update/{order}', [OrderController::class, 'statusUpdate'])
            ->name('orders.status_update');

        Route::post('/order/payment-status-update/{order}', [OrderController::class, 'paymentStatusUpdate'])
            ->name('orders.payment_status_update');

        Route::post('/orders', [OrderController::class, 'filter'])
            ->name('orders.filter');
    });
});
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
;
