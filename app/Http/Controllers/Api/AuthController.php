<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponser;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request) {
       $validatedData = $request->validate([
          'displayName' => 'required|max:55',
          'email' => 'email|required|unique:users',
          'password' => 'required|confirmed',
          'phoneNumber' => 'unique:users'
        ]);

        $createdUser = app('firebase.auth')->createUser($validatedData);
        if(isset($createdUser)) {
          $fbres = app('firebase.auth')->signInWithEmailAndPassword($request['email'], $request['password'])->firebaseUserId();
          $validatedData['password'] = bcrypt($request->password);
          $validatedData['localId'] = $fbres;
          $user = User::create($validatedData);
        }
        return $this->success([
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
      }

      public function login(Request $request) {
        $loginData = $request->validate([
          'email' => 'email|required',
          'password' => 'required'
        ]);
        $auth = app('firebase.auth');
        $loginAttempt = $auth->signInWithEmailAndPassword($loginData['email'], $loginData['password']);
        if (!isset($loginAttempt)) {
          return response(['message' => 'Invalid Credentials']);
        }
        if (!Auth::attempt($loginData)) {
            return $this->error('Credentials not match', 401);
        }

        return $this->success([
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
      }

      public function logout(Request $request) {
        $auth = app('firebase.auth');
        $userId = $request->user();
        $auth->revokeRefreshTokens($userId['localId']);
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
      }

      public function user(Request $request) {
        return response()->json($request->user());
      }
}
