<?php

use Illuminate\Support\Facades\Route;
use Webkul\YapePlin\Http\Controllers\Shop\PaymentController;

Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {
    // Process payment and create order
    Route::get('yapeplin/process', [PaymentController::class, 'process'])
        ->name('yapeplin.process');

    // Upload receipt
    Route::get('yapeplin/upload/{order_id}', [PaymentController::class, 'showUploadForm'])
        ->name('yapeplin.upload');

    Route::post('yapeplin/upload/{order_id}', [PaymentController::class, 'processUpload'])
        ->name('yapeplin.upload.process');
});
