<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Olympus\App as OlympusApplication;
use App\Models\User;
use App\Notifications\User\Register as RegisterNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

// use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
  /**
   * 
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'mobile_phone' => ['required', 'digits:8'],
      'password' => ['required', 'string', 'min:5']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      if (Auth::attempt(['mobile_phone' => $validator['mobile_phone'], 'password' => $validator['password']])) {
        // User is logged
        $user = Auth::user();
        $olApplication = OlympusApplication::getByToken($validator['ol_app_token'], ['title']);
        if (!\is_null($olApplication)) {
          $this->API_RESPONSE['STATUS'] = true;
          $user->tokens()->where('name', $olApplication->title)->delete();
          $this->API_RESPONSE['DATA'] = [
            'profile' => $user,
            'api_token' => $user->createToken($olApplication->title)->plainTextToken,
          ];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['Credenciales incorrectas'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Sudo login
   * @param Request $request
   * @return JsonResponse
   */
  public function sudoLogin(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'mobile_phone' => ['required', 'digits:8'],
      'password' => ['required', 'string', 'min:5']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      if (Auth::attempt(['mobile_phone' => $validator['mobile_phone'], 'password' => $validator['password']])) {
        // User is logged
        $user = Auth::user();
        $olApplication = OlympusApplication::getByToken($validator['ol_app_token'], ['title']);
        if ($user->hasRole(['Developer', 'Admin', 'Vendor']) && !is_null($olApplication)) {
          $user->tokens()->where('name', $olApplication->title)->delete();
          $this->API_RESPONSE['STATUS'] = true;
          $this->API_RESPONSE['DATA'] = [
            'profile' => $user,
            'api_token' => $user->createToken($olApplication->title)->plainTextToken,
          ];
        } else {
          $this->API_RESPONSE['ERORS'] = ['No tiene suficientes permisos'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['Credenciales incorrectas'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * 
   */
  public function register(Request $request)
  {
    // return response()->json($request);
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'name' => ['required', 'string'],
      'mobile_phone' => ['required', 'digits:8', 'unique:users'],
      'password' => ['required', 'confirmed', 'string', 'min:5']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $user = new User([
        'name' => $validator['name'],
        'mobile_phone' => $validator['mobile_phone'],
        'password' => Hash::make($validator['password']),
      ]);
      $user->assignRole('Guest');
      $olApplication = OlympusApplication::getByToken($validator['ol_app_token'], ['title']);
      if ($user->save() && !\is_null($olApplication)) {
        unset($user['roles']);
        $this->API_STATUS = $this->AVAILABLE_STATUS['CREATED'];
        $this->API_RESPONSE['DATA'] = [
          'profile' => $user,
          'api_token' => $user->createToken($olApplication->title)->plainTextToken,
        ];
        $this->API_RESPONSE['STATUS'] = true;
        // Notification::send($user, new RegisterNotification($user));
      } else {
        $this->API_RESPONSE['ERRORS'] = $user->errors;
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Check auth
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function checkAuth()
  {
    if (Auth::check()) {
      $this->API_RESPONSE['STATUS'] = true;
      $this->API_RESPONSE['DATA'] = auth()->user();
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
