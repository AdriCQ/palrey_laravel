<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  /**
   * 
   */
  public function getRoles()
  {
    $user = auth()->user();
    $roles = [];
    foreach ($user->roles as $role) {
      array_push($roles, $role->name);
    }
    return response()->json($roles);
  }

  /**
   * Filter Users
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function filter(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'mobile_phone' => ['nullable', 'digits:8'],
      'name' => ['nullable', 'string']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $usersQry = User::query();
      if (isset($validator['mobile_phone']))
        $usersQry = $usersQry->where('mobile_phone', $validator['mobile_phone']);
      if (isset($validator['name']))
        $usersQry = $usersQry->where('name', 'like', '%' . $validator['name'] . '%');
      $usersQry = $usersQry->orderBy('id', 'desc');
      // TODO: Paginate Data
      $this->API_RESPONSE['DATA'] = $usersQry->get();
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * User Details
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function details(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'mobile_phone' => ['required', 'digits:8']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $user = User::query()->where('mobile_phone', $validator['mobile_phone'])->with('orders');
      if ($user->exists()) {
        $this->API_RESPONSE['DATA'] = $user->first();
        $this->API_RESPONSE['STATUS'] = true;
      } else {
        $this->API_STATUS = $this->AVAILABLE_STATUS['NO_CONTENT'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
