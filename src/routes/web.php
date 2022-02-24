<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;

Route::get('/pay/{order}', [OctoBaseController::class, 'pay'])->name("octo.pay");
Route::get('/verify/{order}', [OctoBaseController::class, 'verify'])->name("octo.verify");
Route::get('/notify/{order}', [OctoBaseController::class, 'notify'])->name("octo.notify");
