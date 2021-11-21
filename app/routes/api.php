<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::delete('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
Route::group([
    'prefix' => 'loans',
    'middleware' => 'auth:api'
], function () {
        Route::post('/', 'LoanController@registerLoan');
    Route::group([
        'prefix' => '{loan}',
    ], function () {
        Route::post('/approve', 'LoanController@approveLoan');
        Route::post('/repayment', 'LoanController@repaymentLoan');
    });
});
