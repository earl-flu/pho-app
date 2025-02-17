<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\PaperDashboardController;
use App\Http\Controllers\PrCategoryController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PrItemController;
use App\Http\Controllers\PrItemDetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrSignatoryController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PurchaseRequestDashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThemeController;
use App\Models\PrItem;
use App\Models\PrSignatory;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/time', function () {
    return [
        'app_time' => now()->toDateTimeString(),
        'server_time' => date('Y-m-d H:i:s'),
    ];
});

Route::get('/clear-cache', function () {
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return 'Caches cleared';
});


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/test', function () {
    return Inertia::render('Welcomev2', []);
});

Route::get('/testdashboardpr', [PurchaseRequestDashboardController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/papers/data', [PaperDashboardController::class, 'getData'])->name('papers.data');
    Route::get('/offices/data', [PaperDashboardController::class, 'getOfficeData'])->name('offices.data');

    Route::get('/papers/dashboard', [PaperDashboardController::class, 'index'])->name('papers.dashboard');
    Route::resource('papers', PaperController::class);
    Route::resource('offices', OfficeController::class)->except(['destroy']);
    Route::resource('tags', TagController::class)->except(['destroy']);

    Route::get('/budgets/{budget}/editAmount', [BudgetController::class, 'editAmount'])->name('budgets.editAmount');
    Route::put('budgets/{budget}/editAmount', [BudgetController::class, 'updateAmount'])->name('budgets.updateAmount');
    Route::resource('budgets', BudgetController::class);

    Route::post('/budgets/{budget}/move', [BudgetController::class, 'moveBudget'])->name('budgets.move');

    Route::get('/json/get-items', [ItemController::class, 'getItems']);
    Route::resource('items', ItemController::class);

    Route::resource('purchase-requests', PurchaseRequestController::class);

    // Categories route
    Route::resource('pr/categories', PrCategoryController::class)
        // ->middleware([
        //     'store' => 'can:create pr library',
        //     'index' => 'can:view pr library',
        // ])
        ->only(['index', 'store']);

    // Items route
    Route::get('pr/categories/{categoryId}/items', [PrItemController::class, 'getItemsByCategory'])->name('categories.items');
    Route::post('pr/categories/{categoryId}/items', [PrItemController::class, 'storeItem'])->name('categories.items.store');

    // Item Details route
    Route::get(
        'pr/categories/{categoryId}/items/{itemId}/details',
        [PrItemDetailController::class, 'getDetailsByItem']
    )->name('items.details.index');
    Route::post('categories/{categoryId}/items/{itemId}/details', [PrItemDetailController::class, 'store'])->name('items.details.store');

    // Edit item detail
    Route::put('/items/details/{id}', [PrItemDetailController::class, 'update'])->name('items.details.update');

    // Prints
    Route::get('/print/purchase-request/{purchaseRequest}', [PrintController::class, 'printPurchaseRequest'])
        ->name('print.purchase-request');

    Route::resource('pr-signatories', PrSignatoryController::class)->except(['destroy', 'show']);



    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::post('/theme/update', [ThemeController::class, 'update'])->name('theme.update');

require __DIR__ . '/auth.php';
