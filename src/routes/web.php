<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;

Route::get('/pay/{order}', [OctoBaseController::class, 'pay'])->name("octo.pay");
Route::get('/verify', [OctoBaseController::class, 'verify'])->name("octo.verify");
Route::get('/notify', [OctoBaseController::class, 'notify'])->name("octo.notify");
