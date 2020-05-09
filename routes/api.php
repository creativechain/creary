<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::get('/blockchain/totalSupply', 'Api\BlockchainController@getTotalSupply')->name('crea.supply');
Route::get('/blockchain/test', 'Api\BlockchainController@markRead');

Route::group(['prefix' => 'notification'], function () {
   Route::get('/{creaUser}', 'Api\NotificationController@index')
       ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.list');

    Route::get('/{creaUser}/unread', 'Api\NotificationController@unread')
        ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.unread');

   Route::get('/{creaUser}/markRead', 'Api\NotificationController@markRead')
        ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.markRead');
});

