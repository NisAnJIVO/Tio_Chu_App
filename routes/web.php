<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarInventoryController;
use App\Http\Controllers\BarSaleController;
use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NightSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QrPaymentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffPaymentController;
use App\Http\Controllers\StoreSaleController;
use Illuminate\Support\Facades\Route;

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Protegidas del Sistema
Route::middleware('auth')->group(function () {

    // Redirección inicial al Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Catálogo e Inventario Base (Módulo 2)
    Route::patch('products/{product}/quick-stock', [ProductController::class, 'updateQuickStock'])->name('products.quickStock');
    Route::resource('products', ProductController::class)->except(['show']);

    // 2. Personal y Turnos (Módulo 3)
    Route::resource('staff', StaffController::class)->except(['show']);

    // 3. Gestión de Noches / Sesiones
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [NightSessionController::class, 'index'])->name('index');
        Route::get('/create', [NightSessionController::class, 'create'])->name('create');
        Route::post('/', [NightSessionController::class, 'store'])->name('store');
        Route::patch('/{session}/status', [NightSessionController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{session}', [NightSessionController::class, 'destroy'])->name('destroy');
    });

    // 3.5 Inventario de Barras (Apertura & Saldos)
    Route::prefix('bar-inventory')->name('barInventory.')->group(function () {
        Route::get('/', [BarInventoryController::class, 'index'])->name('index');
        Route::put('/bulk-update', [BarInventoryController::class, 'updateBulk'])->name('updateBulk');
        Route::post('/sync-previous', [BarInventoryController::class, 'syncFromPreviousNight'])->name('sync');
    });

    // 4. Ventas e Inventario por Barra (Módulo 6)
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [BarSaleController::class, 'index'])->name('index');
        Route::put('/bulk-update', [BarSaleController::class, 'updateBulk'])->name('updateBulk');
        Route::post('/store-sales', [StoreSaleController::class, 'store'])->name('storeSales.store');
        Route::delete('/store-sales/{storeSale}', [StoreSaleController::class, 'destroy'])->name('storeSales.destroy');
        Route::post('/tienda-extras', [StoreSaleController::class, 'updateExtras'])->name('tiendaExtras.update');
        Route::post('/special-mixers', [BarSaleController::class, 'storeSpecialMixer'])->name('specialMixers.store');
    });

    // 5. Facturas Emitidas / Tarjetas / POS (Módulo 1)
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    // 6. Pagos QR (Yasta / Banco Unión & Yape / Banco BCP) (Módulo 4)
    Route::prefix('qrs')->name('qrs.')->group(function () {
        Route::get('/', [QrPaymentController::class, 'index'])->name('index');
        Route::post('/', [QrPaymentController::class, 'store'])->name('store');
        Route::delete('/{qr}', [QrPaymentController::class, 'destroy'])->name('destroy');
    });

    // 7. Pagos al Personal (Módulo Independiente según el Excel)
    Route::prefix('staff-payments')->name('staffPayments.')->group(function () {
        Route::get('/', [StaffPaymentController::class, 'index'])->name('index');
        Route::put('/update', [StaffPaymentController::class, 'update'])->name('update');
        Route::post('/mark-all-paid/{session}', [StaffPaymentController::class, 'markAllPaid'])->name('markAllPaid');
        Route::post('/store', [StaffPaymentController::class, 'store'])->name('store');
        Route::delete('/{attendance}', [StaffPaymentController::class, 'destroy'])->name('destroy');
    });

    // 8. Cierre de Caja y Gastos (Módulo 5)
    Route::prefix('closing')->name('closing.')->group(function () {
        Route::get('/', [CashClosingController::class, 'index'])->name('index');
        Route::post('/expenses', [CashClosingController::class, 'storeExpense'])->name('expenses.store');
        Route::delete('/expenses/{expense}', [CashClosingController::class, 'destroyExpense'])->name('expenses.destroy');
        Route::put('/attendances', [CashClosingController::class, 'updateAttendance'])->name('attendances.update');
        Route::post('/attendances', [CashClosingController::class, 'storeAttendance'])->name('attendances.store');
        Route::delete('/attendances/{attendance}', [CashClosingController::class, 'destroyAttendance'])->name('attendances.destroy');
        Route::post('/{session}/close', [CashClosingController::class, 'closeNight'])->name('close');
        Route::post('/{session}/reopen', [CashClosingController::class, 'reopenNight'])->name('reopen');
    });

});

