<?php

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UsuarioController;

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
    

    $validacao = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',        
        'password' => 'required|string|min:6|confirmed',        
    ]);


    if ($validacao->fails()) {
        return $validacao->errors();
    }

    $imagem = "http://localhost:8000/perfils/profile.jpeg";
    
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),               
        'imagem' => $imagem,               
    ]);
    $user->token = $user->createToken($user->email)->accessToken;    
    return $user;
    
});

Route::post('/login', [UsuarioController::class, 'login']);


Route::middleware('auth:api')->get('/usuario', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->put('/perfil', function (Request $request) {
    $user = $request->user();
    $data = $request->all();
    
    if (isset($data['password'])) {
        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',            
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],                
        ]);
        
        if ($validacao->fails()) {
            return $validacao->errors();
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
    }else{

        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',            
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string|min:6|confirmed',            
        ]);
        
        if ($validacao->fails()) {
            return $validacao->errors();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
    }
    
    if (isset($data['imagem'])) {
        $time = time();
        $diretorioPai = public_path('perfils');
        $diretorioImagem = $diretorioPai . '/perfil_id' . $user->id;
        $diretorioNaUrl = 'http://localhost:8000/perfils/perfil_id' . $user->id;
        
        $ext = substr($data['imagem'], 11, strpos($data['imagem'], ';') - 11);
        $urlImagem = $diretorioImagem . DIRECTORY_SEPARATOR . $time . '.' . $ext;
        $diretorioAsset = $diretorioNaUrl . DIRECTORY_SEPARATOR .$time . '.' . $ext;
        
        $file = str_replace('data:image/' . $ext . ';base64,', '', $data['imagem']);
        $value = $file;
        $file = base64_decode($file);
        

         
        $image = base64_decode($value);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
    
        
        if ($result == 'image/png' || $result == 'image/jpg' || $result == 'image/jpeg') {            
            
        }else{
            return ['imagem' => 'Imagem inválida'];
        }        
              
    
        if (!file_exists($diretorioPai)) {
            mkdir($diretorioPai, 0700, true);
        }
        
        // Remover o arquivo antigo antes de adicionar o mais recente
        if($user->imagem){
            $arrayNameImagem = explode("/",$user->imagem);
            $oldNameImage = end($arrayNameImagem);
            try {
                unlink($diretorioImagem.DIRECTORY_SEPARATOR.$oldNameImage);
            } catch (\Throwable $th) {                
            }            
        }
           
        


        if (!file_exists($diretorioImagem)) {
            mkdir($diretorioImagem, 0700, true);
        }

        file_put_contents($urlImagem, $file);
        
        $user->imagem = $diretorioAsset;
    }
    
    
    $user->save();

    $user->imagem = asset($user->imagem);
    $user->token = $user->createToken($user->email)->accessToken;

    return $user;
});


