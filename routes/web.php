<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('verify-email{userEmail?}{tokenstring?}', 'AuthController@verifyEmail');
Route::get('pdf/{paymentId?}','PaymentController@export_pdf')->name('pdf');
Route::get('export-payout/{email?}{password?}','PaymentController@exportData');
