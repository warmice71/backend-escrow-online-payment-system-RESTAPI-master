<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('verify', 'AuthController@verify');
    Route::post('again', 'AuthController@resendVerify');
    Route::post('verify-answer', 'AuthController@verifyAnswer');
    Route::post('me', 'AuthController@me');
    Route::post('sendpasswordresetlink', 'ResetPasswordController@sendEmail');
    Route::get('admin', 'AdminController@view');
    Route::post('resetpassword', 'ResetPasswordController@resetPassword');
    Route::post('add-image', 'ItemController@store');
    Route::post('add-item', 'ItemController@create');
    Route::post('my-items{page?}', 'ItemController@index');
    Route::post('my-item-detail/{id}', 'ItemController@show');
    Route::post('my-item-search', 'ItemController@searchItem');
    Route::post('payment-intent', 'ItemController@CreatePayIntent');
    Route::post('editProfile', 'AuthController@editProfile');
    Route::post('payments{page?}', 'PaymentController@index');
    Route::post('store-intent', 'PaymentController@storeStripePayment');
    Route::post('create-escrow-transaction', 'PaymentController@completeescrowOrder');
    Route::post('create-escrow-payment', 'PaymentController@storeescrowOnApprove');
    Route::post('send-sellerpayment', 'PaymentController@sendSellerPayment');
    Route::post('complete-escroworder', 'PaymentController@updateescrowOrder');
    Route::post('delete-item', 'ItemController@destroy');
    Route::post('admin/clear-cache', 'AdminController@clearCache');
    Route::post('check-emailverification', 'ItemController@checkEmailVerification');
    Route::post('search-payment', 'PaymentController@searchPayment');
    Route::post('search-paymentadmin', 'AdminController@searchPaymentAdmin');
});