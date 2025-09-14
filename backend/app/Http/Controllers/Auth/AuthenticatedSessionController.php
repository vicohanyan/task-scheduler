<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    /**
     * API login: validate credentials and return Sanctum token.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user  = $request->user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * API logout: revoke current token.
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
