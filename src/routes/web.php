<?php

use Asadbek\OctoLaravel\Http\Controllers\OctoBaseController;
Route::group([
    'middleware' => 'web',
    'prefix' => 'octo',
    'as' => 'octo.',
], function(){
    Route::get('/pay/{order}/{type}', [OctoBaseController::class, 'pay'])->name("pay");
    Route::get('/verify/{order}/{type}', [OctoBaseController::class, 'verify'])->name("verify");
    Route::get('/notify/{order}/{type}', [OctoBaseController::class, 'notify'])->name("notify");

});
