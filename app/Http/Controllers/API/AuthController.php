<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'email'     => 'required|string|email|unique:users,email',
            'password'  => 'required|string|min:4|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors()->all(), 401);
        }
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $fullToken = $user->createToken('user-token')->plainTextToken;

        $token = explode('|', $fullToken)[1];
        return $this->sendResponse([], 'Usuario registrado correctamente.');
    }

    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->sendError('Credenciales incorrectas.', ['El email o la contraseña son incorrectos.'], 401);
        }

        $user = Auth::user();
        // if ($user->status !== 1) {
        //     return $this->sendError('Acceso denegado.', ['Su cuenta no está activa. Contacte al administrador.'], 403);
        // }

        $fullToken = $user->createToken('MyApp')->plainTextToken;
        $token = explode('|', $fullToken)[1];
        $expiresAt = now()->addHour();
        $user->tokens()->latest()->first()->update([
            'expires_at' => $expiresAt,
        ]);

        $data = [
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ];

        return $this->sendResponse($data, 'Inicio de sesión exitoso.');
    }

    public function logout()
    {
        Auth::user()->tokens->each(function ($token) {
            $token->forceDelete();
        });
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Conexión exitosa',
            'resultado' => [
                'status' => 'success',
                'code' => 200,
                'message' => 'Sesión finalizada.',
            ]
        ];
        return response()->json( $response, 200);
    }
}
