<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;

Route::get('/pay', [OctoBaseController::class, 'pay']);
Route::get('/verify', [OctoBaseController::class, 'verify']);
