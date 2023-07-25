<?php

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

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
    
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),               
    ]);
    $user->token = $user->createToken($user->email)->accessToken;
    
    //$idUser = User::find($user->id);
    //return $idUser;
    return $user;
    
});


Route::post('/login', function (Request $request){
    $data = $request->all();
    

    $validacao = Validator::make($data, [        
        'email' => 'required|string|email|max:255',
        'password' => 'required|string',        
    ]);


    if ($validacao->fails()) {
        return $validacao->errors();
    }
    
    
    if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
        
        $user = auth()->user();
        $user->token = $user->createToken($user->email)->accessToken;
       
        return $user;
    }else{
        return ['status' => false];
    }
    
});

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

        
        // Validator::extend('base64image', function($attribute, $value, $parameters, $validator){
        //     $explode = explode(',', $value);
        //     $allow = ['png', 'jpg', 'svg', 'jpeg'];
            
        //     $format = str_replace(
        //         [
        //             'data:image/',
        //             ';',
        //             'base64',                
        //         ],
        //         [
        //             '','','',
        //         ],
        //         $explode[0]            
        //     );

            
            
        //     // check file format
        //     if (!in_array($format, $allow)) {                
        //         return false;
        //     }
            
        //     // //check base64 format
        //     if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
        //         return false;
        //     }
            
        //     return true;        
        // });


        
        
        
        // $validacao = Validator::make($data, [
        //     'imagem' => 'base64image',                        
        // ], ['base64image' => 'Imagem inválida']);

        
        
        // if ($validacao->fails()) {            
        //     return $validacao->errors();
        // }

        

        
        


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
        
        

        // $imagemValida = Validator::make($result, [
        //     'imagem' => 'base64image',                        
        // ], ['base64image' => 'Imagem inválida']);
        
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

    $user->token = asset($user->imagem);
    $user->token = $user->createToken($user->email)->accessToken;

    return $user;
});


