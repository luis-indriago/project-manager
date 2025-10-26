<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
           
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = 'developer';
            $user->assignRole($role);

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(
                data: [
                    'user' => $user->load('roles'),
                    'token' => $token
                ],
                code: 201
            );

        }catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try{
            

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ApiResponse::success(message: 'Las credenciales no son válidas.');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(
                data: [
                    'user' => $user->load('roles'),
                    'token' => $token
                ],
            );

        }catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function user(Request $request)
    {
        try{
            return ApiResponse::success(data: $request->user()->load('roles'));

        }catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try{
            $request->user()->tokens()->delete();

            return ApiResponse::success(message: 'Sesión cerrada correctamente');

        }catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }
}
