<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;

Route::get('/pay/{shop_transaction_id}', [OctoBaseController::class, 'pay'])->name("octo.pay");
Route::get('/verify', [OctoBaseController::class, 'verify'])->name("octo.verify");
