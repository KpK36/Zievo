<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $user = User::query()->create([
                    'name'     => $request->validated('name'),
                    'email'    => $request->validated('email'),
                    'password' => Hash::make($request->validated('password')),
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                return ['user' => $user, 'token' => $token];
            });

            return response()->json([
                'user'         => $result['user'],
                'access_token' => $result['token'],
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'error'   => $exception->getMessage(),
                'message' => 'Erro ao registrar o usuario',
            ]);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'message'      => 'Autenticado com sucesso.'
        ], 200);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
