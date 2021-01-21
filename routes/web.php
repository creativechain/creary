<?php

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

use Illuminate\Support\Facades\Route;

Route::get('/', 'CrearyController@home')->name('home');

Route::get('/search', 'CrearyController@search')->name('search');
Route::get('/accounts/search', 'CrearyController@accountsSearch')->name('accounts.search');
Route::get('/welcome', 'CrearyController@welcome')->name('welcome');
Route::get('/validate', 'CrearyController@welcome')->name('validate');
Route::get('/~witness', 'CrearyController@witnesses')->name('witnesses');
Route::get('/publish', 'CrearyController@publish')->name('publish');
Route::get('/explore', 'CrearyController@explore')->name('explore');
Route::get('/~market', 'CrearyController@market')->name('market');
Route::get('/faq', 'CrearyController@faq')->name('faq');
Route::get('/recover-account', 'CrearyController@recoverAccount')->name('recoverAccount');
Route::get('/terms_and_conditions', 'CrearyController@terms')->name('terms');
Route::get('/privacy_policy', 'CrearyController@privacy')->name('privacy');

Route::get('/{category}', 'CrearyController@home')
    ->where('category', '^(skyrockets|votes|responses|popular|promoted|cashout|payout|now|active|search)$');

Route::get('/{category}/{tag}', 'CrearyController@home')
    ->where('category', '^(skyrockets|votes|responses|popular|promoted|cashout|payout|now|active)$')
    ->where('tag', '^([\w\d\-\/]+)$');

Route::get('/{user}', 'CrearyController@profile')
    ->where('user', '^(@[\w\.\d-]+)$');

Route::get('/{user}/feed', 'CrearyController@home')
    ->where('user', '^(@[\w\.\d-]+)$');

Route::get('/{user}/{section}', 'CrearyController@profileSection')
    ->where('user', '^(@[\w\.\d-]+)$')
    ->where('section', '^(projects|following|followers|curation-rewards|author-rewards|blocked|wallet|settings|passwords|balances|permissions|notifications)$');

Route::get('/{category}/{user}/{permlink}', 'CrearyController@postCategory')
    ->where('category', '^([\w\d\-\/]+)$')
    ->where('user', '^(\@[\w\d\.-]+)$')
    ->where('permlink', '^([\w\d-]+)$');

Route::get('/{user}/{permlink}', 'CrearyController@post')
    ->where('user', '^(\@[\w\d\.-]+)$')
    ->where('permlink', '^([\w\d-]+)$');
