<?php

use Illuminate\Support\Facades\Route;
use Webkul\YapePlin\Http\Controllers\Admin\ReceiptController;

Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    Route::prefix('yapeplin')->name('admin.yapeplin.')->group(function () {
        Route::get('receipts', [ReceiptController::class, 'index'])
            ->name('receipts.index');

        Route::get('receipts/{id}', [ReceiptController::class, 'show'])
            ->name('receipts.show');

        Route::post('receipts/{id}/approve', [ReceiptController::class, 'approve'])
            ->name('receipts.approve');

        Route::post('receipts/{id}/reject', [ReceiptController::class, 'reject'])
            ->name('receipts.reject');
    });
});
