<?php
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PostController;

// endpoints for frontend
Route::get('/api/destinations', [DestinationController::class, 'index']);
Route::get('/api/destination/recomendation', [DestinationController::class, 'getRecommendedDestination']);
Route::get('/api/destination/recommendationByUser', [DestinationController::class, 'getRecommendedDestinationByUser']);
Route::get('/api/regions', [RegionController::class, 'getRegion']);
Route::get('/api/destination/{id}', [DestinationController::class, 'getDestinationDetail']);
Route::get('/api/packages/{id}', [PackageController::class, 'getPackageDetail']);
Route::post('api/order', [OrderController::class, 'store'])->name('order.store');
Route::post('api/order/approve', [OrderController::class, 'store'])->name('order.approve');
Route::get('api/orders/history', [OrderController::class, 'orderHistory']);
Route::get('api/categories', [CategoryController::class, 'getCategory']);
Route::get('/api/destinations/{category}', [DestinationController::class, 'filterByCategory']);
Route::get('/api/destination/{id}/lowest-price', [DestinationController::class, 'getLowestPrice']);
Route::get('/api/destination/search/{keyword}', [DestinationController::class, 'searchDestination']);

// Like
Route::post('/api/destination/{id}/like', action: [FavoriteController::class, 'likeDestination'])->middleware('auth');
Route::get('/api/profile/favorite', [FavoriteController::class, 'getFavoriteDestinations']);

// Post/Article
Route::get('/api/articles', [PostController::class, 'index']);
Route::get('/api/articles/latest', [PostController::class, 'getLatestPost']);
Route::get('/api/article/{id}', [PostController::class, 'getPostDetail']);

require __DIR__ . '/auth.php';

