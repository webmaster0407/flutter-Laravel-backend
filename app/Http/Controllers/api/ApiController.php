<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not crate token'
                ], 500);
        }

        $user = auth()->user();

        return response()->json(compact('token', 'user'));
    }

    public function getAuthenticatedUser() {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'User not found'
                ], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'Token Expired'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenIInvalidException $e) {
            return response()->json([
                'Token invalid'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'Token absent'
            ], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function register(Request $request) {
        // name: TestName
        // email: Test@example.com
        // password: secret
        // password_confirmation : secret

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = new User;
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->role_id = 2;
        $user->save();

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function logout(Request $request) {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::parseToken()->invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User Logged out successfully!'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry , the user cannot be logged out'
            ], 500);
        }
    }

    public function changePassword() {
        try {
          $param = $request->request->all();
          if (Hash::check($this->getValue("cpassword", $param), auth()->user()->password, [])) {
            $user = DB::table('users')
              ->where("id", auth()->user()->id)
              ->update(["password" => bcrypt($this->getValue("npassword", $param))]);
            if ($user > 0) {
              $data_response = array(
                'status' => 1,
                'message' => 'Se actualizó la contraseña',
                'id' => auth()->user()->id
              );
            } else {
              $data_response = array(
                'status' => 0,
                'message' => 'Falló la actualización de la contraseña',
                'id' => auth()->user()->id
              );
            }
          } else {
            $data_response = array(
              'status' => 2,
              'message' => 'Tu contraseña actual no coincide',
              'id' => auth()->user()->id
            );
          }
        } catch (Exception $e) {
          $data_response = array(
            'status' => 0,
            'message' => 'Error de sistema',
            'id' => auth()->user()->id
          );
        }

        return response()->json();


        // $cnewPassword = request('password');
        // $user = User::find(auth()->user()->id);
        // $user->password = bcrypt($cnewPassword);
        // $user->save();

        // return response()->json(['message' => 'Password was changed successfully'], 200);
    }

    public function dashboard() {
        $data = 'Welcome to dashboard';

        return response()->json([
            'data' => $data
        ], 200);
    }


    public function saveFCMToken() {
        $token = request('fcm_token');

        // check existence of token
        $check = \App\Models\FcmToken::where('fcm_token', '=', $token)->count();
        if ($check == 0) {
            // save it
            $fcmToken = new \App\Models\FcmToken;
            $fcmToken->fcm_token = $token;
            $fcmToken->save();
        }

        return response()->json(["msg" => "Token save successfully!"], 200);
    }

}
