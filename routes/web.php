<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Customer Management Routes
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::resource('customers', CustomerController::class)->except(['create', 'edit', 'show']);

    // Invoice Management Routes
    Route::post('/invoices/import', [InvoiceController::class, 'import'])->name('invoices.import');
    Route::get('/invoices/template', [InvoiceController::class, 'downloadTemplate'])->name('invoices.template');
    Route::post('/invoices/bulk-send-email', [EmailController::class, 'sendBulk'])->name('invoices.bulk-send-email');
    Route::post('/invoices/{invoice}/send-email', [EmailController::class, 'send'])->name('invoices.send-email');
    Route::post('/invoices/{invoice}/retry-email', [EmailController::class, 'retry'])->name('invoices.retry-email');
    Route::get('/invoices/{invoice}/pdf', [PdfController::class, 'downloadInvoice'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/stream', [PdfController::class, 'streamInvoice'])->name('invoices.stream');
    Route::get('/invoices/{invoice}/billing-detail/pdf', [PdfController::class, 'downloadBillingDetail'])->name('invoices.billing-detail.pdf');
    Route::get('/invoices/{invoice}/receipt/pdf', [PdfController::class, 'downloadReceipt'])->name('invoices.receipt.pdf');
    Route::get('/invoices/{invoice}/tax-invoice/pdf', [PdfController::class, 'downloadTaxInvoice'])->name('invoices.tax-invoice.pdf');
    Route::resource('invoices', InvoiceController::class);

    // System Management Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-smtp', [SettingController::class, 'testSmtp'])->name('settings.test-smtp');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
