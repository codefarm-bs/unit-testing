<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        return $this->success(['token' => $this->getPlainTextToken($user)]);

    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return $this->error('Credentials not match', 401);
        }

        return $this->success(['token' => $this->getPlainTextToken(Auth::user())]);
    }

    public function getPlainTextToken($user)
    {
        return $user->createToken('API Token')->plainTextToken;
    }
}
