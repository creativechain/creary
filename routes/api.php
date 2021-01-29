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

Route::group(['middleware' => ['cors']], function () {

    Route::group(['prefix' => 'accounts'], function () {

        Route::middleware(['throttle:search'])->get('/search', 'Api\AccountsController@search')->name('accounts.search');
    });

    Route::group(['prefix' => 'tags'], function () {

        Route::get('/', 'Api\TagsController@index')->name('tags.index');
        Route::get('/active', 'Api\TagsController@mostActiveTags')->name('tags.active');
        Route::middleware(['throttle:search'])->get('/search', 'Api\TagsController@search')->name('tags.search');
    });

    Route::group(['prefix' => 'comments'], function () {

        //Route::get('/', 'Api\TagsController@index')->name('tags.index');
        Route::middleware(['throttle:search'])->get('/feed', 'Api\CommentsController@feed')->name('comments.feed');
        Route::middleware(['throttle:search'])->get('/searchByReward', 'Api\CommentsController@searchByReward')->name('comments.searchByReward');
        Route::get('/multiple', 'Api\CommentsController@showMultiple')->name('comments.show.multiple');
        Route::get('/{author}/portfolio', 'Api\CommentsController@portfolio')->name('comments.portfolio')
            ->where('author', '^(@[\w\.\d-]+)$');

        Route::get('/{author}/{permlink}', 'Api\CommentsController@show')->name('comments.show')
            ->where('author', '^(\@[\w\d\.-]+)$')
            ->where('permlink', '^([\w\d-]+)$');
    });
});


/*Route::get('/votes/{creaUser}', 'CrearyController@testVotes')
    ->where('creaUser', '^([\w\.\d-]+)$');*/
