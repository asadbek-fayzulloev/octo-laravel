<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;
Route::group([
    'middleware' => 'web',
    'prefix' => 'octo',
    'as' => 'octo.',
], function(){
    Route::get('/pay/{order}', [OctoBaseController::class, 'pay'])->name("pay");
    Route::get('/verify/{order}', [OctoBaseController::class, 'verify'])->name("verify");
    Route::get('/notify/{order}', [OctoBaseController::class, 'notify'])->name("notify");

});
