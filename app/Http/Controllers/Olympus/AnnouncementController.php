<?php

namespace App\Http\Controllers\Olympus;

use App\Http\Controllers\Controller;
use App\Models\Olympus\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
  /**
   * Get Announcements
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function list(Request $request)
  {
    $this->API_RESPONSE['DATA'] = Announcement::query()->where('active', true)->whereDate('updated_at', '>', now()->addDays(-5))->get();
    $this->API_RESPONSE['STATUS'] = true;
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Create
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'type' => ['required', 'in:' . implode(',', Announcement::$TYPES)],
      'title' => ['required', 'string'],
      'link' => ['required', 'string'],
      'text' => ['nullable', 'string'],
      'html' => ['nullable', 'string'],
      'icon' => ['nullable', 'string'],
      // 'image' => ['nullable', 'image']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      // Upload image if exists
      // if (isset($validator['image'])) {
      //   $imageFile = $validator['image'];
      //   $imageModel = new Image();
      //   $imageModel->uploadImage($imageFile, 'announcement', ['md']);
      //   $imageModel->keywords = ['announcement'];
      //   $imageModel->title = 'Announcement';
      // }
      $model = new Announcement([
        'type' => $validator['type'],
        'title' => $validator['title'],
        'link' => $validator['link'],
        'text' => $validator['text'],
        'html' => $validator['html'],
        'icon' => $validator['icon'],
      ]);
      if ($model->save()) {
        $this->API_RESPONSE = $model;
      } else {
        $this->API_STATUS = 503;
        $this->API_RESPONSE = $model->errors;
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
