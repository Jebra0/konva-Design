<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TestController;
use App\Models\TemplateCategory;
use App\Models\Text;
use App\Repositories\Cart\CartModelRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {

    $categories = TemplateCategory::select('id', 'name')->get();

    $shapes = getShapes();

    $tmplateImages = getImages();

    $isAdmin = isAdmin();

    $user = Auth::user();

    $repo = new CartModelRepository();
    $cart = $repo->get();

    $user_images = '';

    if ($user) {
        $user_images = $user->images()->orderBy('created_at', 'desc')
            ->get();
    }

    return Inertia::render('DesignPage', [
        'categories' => $categories,
        'shapeTemplates' => $shapes,
        'templateImages' => $tmplateImages,
        'isAdmin' => $isAdmin,
        'user' => $user,
        'user_images' => $user_images,
        'items' => $cart->count(),

    ]);
})->name('home');

// get myDesigns data
Route::get('/get/design', function () {
    $user = Auth::user();
    return $user->designs()
        ->orderBy('created_at', 'desc')
        ->paginate(10);
});

// get texts data
Route::get('/texts', function(){
    return Text::select(['id', 'image', 'user_id'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);
});

// templates
Route::group([
    'prefix' => "template",
    'as' => "template."
], function () {
    Route::group([
        'middleware' => "auth",
    ], function () {
        Route::get('/', [TemplateController::class, 'getAllTemplates'])->name('all');

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

    Route::post('/', [CheckoutController::class, 'index'])
        ->name('index');

    Route::post('/store', [CheckoutController::class, 'store'])
        ->name('store');
});

// payment
Route::group([
    'prefix' => "payment",
    'middleware' => "auth",
    'as' => "payment."
], function (){
    Route::get('/{order}', [PaymentController::class, 'index'])
        ->name('index');

    Route::post('/stripe/create/{order}', [PaymentController::class, 'createStripePaymentIntent'])
        ->name('stripe.create');

    Route::get('/stripe/callback/{order}', [PaymentController::class, 'confirm'])
        ->name('stripe.callback');
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

// socialize
Route::group([
    'prefix' => "auth",
    'as' => "auth.socialite."
], function (){
    Route::get('/{provider}/redirect', [SocialLoginController::class, 'redirect'])
        ->name('redirect');

    Route::get('/{provider}/callback', [SocialLoginController::class, 'callback'])
        ->name('callback');
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
