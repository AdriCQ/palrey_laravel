<?php

namespace App\Http\Controllers\Olympus;

use App\Http\Controllers\Controller;
use App\Models\Olympus\App as OlympusApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller
{
  /**
   * 
   */
  public function getToken(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'app_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $appQry  = OlympusApplication::query()->find($validator['app_id']);
      if ($appQry) {
        // TODO: Check if user is owner or admin
        $this->API_RESPONSE['DATA'] = [
          'ol_app_token' => $appQry->id . '|' . htmlentities(Hash::make($appQry->token))
        ];
        $this->API_RESPONSE['STATUS'] = true;
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No existe'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * getInfo
   * @param Request request
   * @return JsonResponse
   */
  public function getInfo(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'app_token' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $app = OlympusApplication::getByToken($validator['app_token']);
      if ($app) {
        $this->API_RESPONSE['DATA'] = $app;
        $this->API_RESPONSE['STATUS'] = true;
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * checkForUpdates
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function checkForUpdates(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'ol_app_version' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $appUpdates = OlympusApplication::checkForUpdates($validator['ol_app_token'], $validator['ol_app_version']);
      $this->API_RESPONSE['STATUS'] = true;
      if ($appUpdates) {
        $this->API_RESPONSE['DATA'] = ['info' => $appUpdates, 'download_url' => url('/olympus/app/dowload', [
          'ol_app_token' => $validator['ol_app_token']
        ])];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Download Application
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function download(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string']
    ]);
    if (!$validator->fails()) {
      $validator = $validator->validate();
      $app = OlympusApplication::getByToken($validator['ol_app_token']);
      if ($app) {
        return $app->download();
      }
    }
    return view('welcome');
  }
}
