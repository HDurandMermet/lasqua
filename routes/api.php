<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\ChatroomController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('songs', [SongController::class, 'getAllSongs']);
    Route::get('songs/{song_id}', [SongController::class, 'getSong']);
    Route::post('songs', [SongController::class, 'createSong']);
    Route::put('songs/{id}', [SongController::class, 'updateSong']);
    Route::delete('songs/{id}',[SongController::class, 'deleteSong']);


    Route::get('chat/{room}', [ChatroomController::class, 'getAllMsgs']);
    Route::get('chat/{room}/{msg_id}', [ChatroomController::class, 'getMsg']);
    Route::post('chat/{room}', [ChatroomController::class, 'createMsg']);
    Route::put('chat/{room}/{id}', [ChatroomController::class, 'updateMsg']);
    Route::delete('chat/{room}/{id}',[ChatroomController::class, 'deleteMsg']);
});

Route::get('test', [SongController::class, 'getAllSongs']);
Route::get('caca', function() {
    echo 'caca';
});
