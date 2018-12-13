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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('upload')->group(function(){

    Route::post('goods','UploadController@goodsPicUpload')->name('uploadGoods');
    Route::post('delete','UploadController@fileDelete')->name('uploadDelete');
    Route::post('store', 'UploadController@storeLogo');
    Route::post('member', 'UploadController@memberPicUpload');
    Route::post('company', 'UploadController@companyAvater');
});



