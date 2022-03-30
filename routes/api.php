<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\ApiController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Auth API
Route::post('auth', 'App\Http\Controllers\api\ApiController@authenticate');
Route::post('register', 'App\Http\Controllers\api\ApiController@register');
Route::post('logout', 'App\Http\Controllers\api\ApiController@logout');


// Receive token from mobile
Route::post('fcm/token', 'App\Http\Controllers\api\ApiController@saveFCMToken');

Route::group(['middleware' => ['auth.jwt']], function() {
    //Authenticated routes go here!
    Route::get('/test', function() {
        return "Hello World!";
    });

    // Auth API
    Route::get('me', 'App\Http\Controllers\api\ApiController@getAuthenticatedUser');
    Route::post('settings/changePassword', 'App\Http\Controllers\api\ApiController@changePassword');

    // Dashboard API
    Route::get('dashboard', 'App\Http\Controllers\api\ApiController@dashboard');
});