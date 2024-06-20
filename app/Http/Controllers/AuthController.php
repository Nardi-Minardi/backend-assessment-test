<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends BaseController
{
  public function login(LoginRequest $request): JsonResponse
  {
    try {
      $user = User::where('email', $request->email)->first();
      if ($user) {
          if (Hash::check($request->password, $user->password)) {
              $token = $user->createToken('backend-assessment-test')->accessToken;
              $response = ["user" => $user, "token" => $token];
              return response()->json($response, HttpResponse::HTTP_OK);
          } else {
              $response = ["message" => "Password mismatch"];
              return response()->json($response, HttpResponse::HTTP_UNAUTHORIZED);
          }
      } else {
          $response = ["message" =>'User does not exist'];
          return response()->json($response, HttpResponse::HTTP_UNAUTHORIZED);
      }
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function logout (Request $request) 
  {
    try {
      if(Auth::check()) {
        Auth::user()->token()->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, HttpResponse::HTTP_OK);
      } else {
        $response = ['message' => 'You are not logged in!'];
        return response($response, HttpResponse::HTTP_UNAUTHORIZED);
      }
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
