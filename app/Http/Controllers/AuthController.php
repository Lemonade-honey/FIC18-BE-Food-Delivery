<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function registerPost(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:255', 'min:3', 'regex:/^[^\d]*$/'],
            'email' => ['required', 'email', 'max:255', 'min:3', 'unique:users'],
            'phone' => ['required', 'numeric', 'min:6', 'unique:users'],
            'password' => ['required', 'min:6']
        ], [
            'name.regex' => 'name tidak boleh mengandung angka'
        ]);

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => $request->input('password')
            ]);

            Log::info('create new user', [
                'data' => $user
            ]);

            return response()->json([
                'data' => new UserResource($user)
            ], 201);
        }
        
        catch (Throwable $th) {
            Log::critical('user baru gagal dibuat. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function loginPost(Request $request): JsonResponse
    {
        $request->validate([
            'credential' => ['required', 'max:255'],
            'password' => 'required'
        ]);

        try {
            // check credential type
            $fieldType = filter_var($request->input('credential'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $user = User::where($fieldType, $request->input('credential'))->first();

            if(! $user || ! \Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password))
            {
                return response()->json([
                    'errors' => [
                        'massage' => "password dan $fieldType yang dimasukan salah"
                    ]
                ], 401);
            }

            Log::info('user ' . $user->email . ' login.');

            // create token
            $user->token = $user->createToken('basic-token')->plainTextToken;

            return response()->json([
                'data' => new UserResource($user)
            ]);
        }
        
        catch (Throwable $th) {
            Log::critical('user gagal login. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function logoutDelete(Request $request): JsonResponse
    {
        Log::info('user logout', [$request->user()]);

        $request->user()->tokens()->delete();

        return response()->json([
            'data' => true
        ]);
    }
}
