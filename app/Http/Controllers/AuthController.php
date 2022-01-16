<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use jeremykenedy\LaravelRoles\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email'                 =>  'email|max:255|exists:users,email',
            'password'              =>  'string|between:8,255'
        ],
        [
            'email.email'           =>  'El Correo Electrónico tiene un formato inválido',
            'email.max'             =>  'El Correo Electrónico debe tener una longitud máxima de 255 caracteres',
            'email.exists'          =>  'El Correo Electrónico y/o la Contraseña no coinciden',
            'password.string'       =>  'La Contraseña contiene caracteres inválidos',
            'password.between'      =>  'La Contraseña debe contener entre 8 y 255 caracteres',
        ]);

        if($validator->fails())
            return response()->json(['errors'   =>  $validator->errors()], 422);

        if(User::where('email', $request->input('email'))->count() > 0) {

            $credentials = $request->only('email', 'password');

            if ($token = $this->guard()->attempt($credentials)) {

                return response()->json(
                    [
                        'status'            => 'success',
                        'Authorization'     =>  $token,
                    ], 200)->header('Authorization', $token);
            }

            return response()->json(['error' => 'El Correo Electrónico y/o la Contraseña no coinciden'], 422);

        }

        return response()->json(['error' => 'Usuario no existe'], 422);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs', 'Authorization' => $token], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 422);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function payload()
    {
        return response()->json(auth()->payload());
    }

    private function guard()
    {
        return Auth::guard();
    }

}
