<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ExpiredStockController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');
    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('stock-ins', [StockInController::class, 'index'])->name('stock-ins.index');
    Route::get('stock-ins/create', [StockInController::class, 'create'])->name('stock-ins.create');
    Route::post('stock-ins', [StockInController::class, 'store'])->name('stock-ins.store');
    Route::get('adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
    Route::resource('sales-orders', SalesOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::post('purchase-orders/{purchaseOrder}/order', [PurchaseOrderController::class, 'markOrdered'])->name('purchase-orders.order');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::post('sales-orders/{salesOrder}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
    Route::post('sales-orders/{salesOrder}/dispense', [SalesOrderController::class, 'dispense'])->name('sales-orders.dispense');
    Route::post('sales-orders/{salesOrder}/cancel', [SalesOrderController::class, 'cancel'])->name('sales-orders.cancel');
    Route::post('sales-orders/{salesOrder}/payments', [SalesOrderController::class, 'recordPayment'])->name('sales-orders.payments.store');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::get('my-permission-requests', [PermissionRequestController::class, 'mine'])->name('permission-requests.mine');
    Route::post('my-permission-requests', [PermissionRequestController::class, 'store'])->name('permission-requests.store');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/archived', [CategoryController::class, 'archived'])->name('categories.archived');
    Route::post('categories/{categoryId}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{categoryId}/permanently-delete', [CategoryController::class, 'permanentlyDelete'])->name('categories.permanently-delete');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/archived', [UserController::class, 'archived'])->name('users.archived');
    Route::post('users/{userId}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('products/archived', [ProductController::class, 'archived'])->name('products.archived');
    Route::post('products/{productId}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::get('suppliers/archived', [SupplierController::class, 'archived'])->name('suppliers.archived');
    Route::post('suppliers/{supplierId}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::post('stock-ins/{stockIn}/approve', [StockInController::class, 'approve'])->name('stock-ins.approve');
    Route::post('adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
    Route::post('adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('adjustments.reject');
    Route::get('reports', ReportController::class)->name('reports.index');
    Route::get('permission-requests', [PermissionRequestController::class, 'index'])->name('permission-requests.index');
    Route::post('permission-requests/{permissionRequest}/approve', [PermissionRequestController::class, 'approve'])->name('permission-requests.approve');
    Route::post('permission-requests/{permissionRequest}/reject', [PermissionRequestController::class, 'reject'])->name('permission-requests.reject');
    Route::get('expired-stock', [ExpiredStockController::class, 'index'])->name('expired-stock.index');
    Route::post('expired-stock/cleanup', [ExpiredStockController::class, 'cleanup'])->name('expired-stock.cleanup');
});
