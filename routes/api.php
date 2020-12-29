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

Route::get('/blockchain/supply', 'Api\BlockchainController@getSupply')->name('crea.supply');
Route::get('/blockchain/currentSupply', 'Api\BlockchainController@getCurrentSupply')->name('crea.supply.current');
Route::get('/blockchain/totalSupply', 'Api\BlockchainController@getTotalSupply')->name('crea.supply.total');
Route::get('/blockchain/test', 'Api\BlockchainController@markRead');

Route::group(['prefix' => 'notification'], function () {
   Route::get('/{creaUser}', 'Api\NotificationController@index')
       ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.list');

    Route::get('/{creaUser}/unread', 'Api\NotificationController@unread')
        ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.unread');

   Route::get('/{creaUser}/markRead', 'Api\NotificationController@markRead')
        ->where('creaUser', '^(@[\w\.\d-]+)$')->name('api.notification.markRead');
});

Route::group(['prefix' => 'accounts', 'middleware' => ['cors']], function () {

    Route::middleware(['throttle:500,1'])->get('/search', 'Api\AccountsController@search')->name('accounts.search');
});

Route::group(['prefix' => 'tags', 'middleware' => ['cors']], function () {

    Route::get('/', 'Api\TagsController@index')->name('tags.index');
    Route::middleware(['throttle:500,1'])->get('/search', 'Api\TagsController@search')->name('tags.search');
});

Route::group(['prefix' => 'comments', 'middleware' => ['cors']], function () {

    //Route::get('/', 'Api\TagsController@index')->name('tags.index');
    Route::middleware(['throttle:500,1'])->get('/feed', 'Api\CommentsController@feed')->name('comments.feed');
    Route::middleware(['throttle:500,1'])->get('/searchByReward', 'Api\CommentsController@searchByReward')->name('comments.searchByReward');
    Route::get('/multiple', 'Api\CommentsController@showMultiple')->name('comments.show.multiple');
    Route::get('/{author}/{permlink}', 'Api\CommentsController@show')->name('comments.show');
});

/*Route::get('/votes/{creaUser}', 'CrearyController@testVotes')
    ->where('creaUser', '^([\w\.\d-]+)$');*/
