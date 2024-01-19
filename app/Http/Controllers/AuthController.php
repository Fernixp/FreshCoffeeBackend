<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
     
    public function register(RegistroRequest $request){
        //Validar el registro
        $data = $request->validated();

        //Crear el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        //Retornar una respuesta
        return [
            /* Generando un token y retornando en texto plano */
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];
    }

    public function login(LoginRequest $request){

        $data = $request->validated();

        //Revisamos el password
        if (!Auth::attempt($data)) {
            //En caso de que no se pueda autenticar, cambiamos el estatus a 422 para mandar un error de axios
            return response([
                'errors' => ['El email o la contraseña son incorrectos']
            ], 422); //si no cambiamos el estatus a 422 para mandar un error, no se mostrara ningun error, lo tomara coomo que todo esta bien
        }

        //Autenticar el usuario
        $user = Auth::user();
        return [
            /* Generando un token y retornando en texto plano */
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];
    }


    public function logout(Request $request){
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return [
            'user' => null
        ];
    }
}
