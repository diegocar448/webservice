<?php

use App\Models\User;
use App\Models\Conteudo;
use App\Models\Comentario;

use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConteudoController;



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

Route::post('/cadastro', [UsuarioController::class, 'cadastro']);
Route::post('/login', [UsuarioController::class, 'login']);
Route::middleware('auth:api')->put('/perfil', [UsuarioController::class, 'perfil']);
Route::middleware('auth:api')->post('/conteudo/adicionar', [ConteudoController::class, 'adicionar']);


Route::get('/testes', function(){
    
    $user = User::find(1);
    $user2 = User::find(2);

    //modelo de como adicionar conteudos
    // $user = User::find(1);
    // $user->conteudos()->create([
    //     'titulo' => 'Conteudo3',
    //     'texto' => 'Aqui um texto',
    //     'imagem' => 'url da imagem',
    //     'link' => 'Link',
    //     'data' => '2023-07-27',
    // ]);
    // return $user->conteudos;


    
    // adicionar amigos
    //$user->amigos()->attach($user2->id);
    //$user->amigos()->detach($user2->id);
    //$user->amigos()->toggle($user2->id);
    
    
    // adicionar curtidas
    // $conteudo = Conteudo::find(1);
    // $user->curtidas()->toggle($conteudo->id);

    // return $conteudo->curtidas()->count();
    //return $conteudo->curtidas();

    // adicionar comentarios
    $conteudo = Conteudo::find(1);
    $user->comentarios()->create([
        'conteudo_id' => $conteudo->id,
        'texto' => 'Show',        
        'data' => date('Y-m-d'),
    ]);
    $user2->comentarios()->create([
        'conteudo_id' => $conteudo->id,
        'texto' => 'Nop',        
        'data' => date('Y-m-d'),
    ]);
    return $user2->comentarios;

});


