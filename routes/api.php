<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
// s});

Route::post('/cadastro', function (Request $request){
    $data = $request->all();
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        
    ]);
    
    $user->token = $user->createToken($user->email)->accessToken;

    return $user;
    
});

Route::middleware('auth:api')->get('/usuario', function (Request $request) {
    return $request->user();
});

