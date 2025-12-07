<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\VideoController;
use App\Http\Controllers\Customer\AccessRequestController;
use App\Http\Controllers\Admin\AdminAccessRequestController;

// Public home - video list (no login required)
Route::get('/', [VideoController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('customer.videos.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public routes - no authentication required
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer routes - require authentication
    Route::prefix('customer')->name('customer.')->group(function () {
        // My videos dashboard
        Route::get('/videos', [VideoController::class, 'myVideos'])->name('videos.index');

        // Video watching (requires authentication and active access)
        Route::get('/videos/{video}/watch', [VideoController::class, 'watch'])->name('videos.watch');
        Route::get('/videos/{video}/stream', [VideoController::class, 'stream'])->name('videos.stream');

        // Access requests
        Route::get('/access-requests', [AccessRequestController::class, 'index'])->name('access-requests.index');
        Route::get('/access-requests/create', [AccessRequestController::class, 'create'])->name('access-requests.create');
        Route::post('/access-requests', [AccessRequestController::class, 'store'])->name('access-requests.store');
        Route::delete('/access-requests/{accessRequest}/cancel', [AccessRequestController::class, 'cancel'])->name('access-requests.cancel');
    });

    // Admin routes - require admin role
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        // Customer management
        Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);
        Route::post('customers/{customer}/toggle-status', [App\Http\Controllers\Admin\CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
        Route::post('customers/{customer}/send-password-reset', [App\Http\Controllers\Admin\CustomerController::class, 'sendPasswordReset'])->name('customers.send-password-reset');

        // Video management
        Route::resource('videos', App\Http\Controllers\Admin\VideoController::class);

        // Access request management
        Route::get('access-requests', [AdminAccessRequestController::class, 'index'])->name('access-requests.index');
        Route::get('access-requests/{accessRequest}', [AdminAccessRequestController::class, 'show'])->name('access-requests.show');
        Route::post('access-requests/{accessRequest}/approve', [AdminAccessRequestController::class, 'approve'])->name('access-requests.approve');
        Route::post('access-requests/{accessRequest}/reject', [AdminAccessRequestController::class, 'reject'])->name('access-requests.reject');
        Route::post('access-requests/bulk-approve', [AdminAccessRequestController::class, 'bulkApprove'])->name('access-requests.bulk-approve');
    });
});

require __DIR__ . '/auth.php';
