<?php

namespace App\Http\Controllers\Olympus;

use App\Http\Controllers\Controller;
use App\Models\Olympus\Announcement;
use App\Models\Shop\Image;
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
    $this->API_RESPONSE['DATA'] = Announcement::query()->where('active', true)->with('image')->orderBy('updated_at', 'desc')->get();
    $this->API_RESPONSE['STATUS'] = true;
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Get Announcements
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vList(Request $request)
  {
    $this->API_RESPONSE['DATA'] = Announcement::query()->with('image')->orderBy('updated_at', 'desc')->get();
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
      'link' => ['nullable', 'string'],
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

  /**
   * Update
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function update(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'announcement_id' => ['required', 'integer'],
      'type' => ['nullable', 'in:' . implode(',', Announcement::$TYPES)],
      'title' => ['nullable', 'string'],
      'link' => ['nullable', 'string'],
      'text' => ['nullable', 'string'],
      'html' => ['nullable', 'string'],
      'icon' => ['nullable', 'string'],
      'active' => ['nullable', 'boolean'],
      'image' => ['nullable', 'image']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $model = Announcement::query()->find($validator['announcement_id']);
      if ($model) {
        if (isset($validator['type']))
          $model->type = $validator['type'];
        if (isset($validator['title']))
          $model->title = $validator['title'];
        if (isset($validator['link']))
          $model->link = $validator['link'];
        if (isset($validator['text']))
          $model->text = $validator['text'];
        if (isset($validator['html']))
          $model->html = $validator['html'];
        if (isset($validator['icon']))
          $model->icon = $validator['icon'];
        if (isset($validator['active']))
          $model->active = $validator['active'];
        if (!isset($validator['image']))
          $model->image_id = null;
        if ($model->save()) {
          $this->API_RESPONSE['STATUS'] = true;
          $this->API_RESPONSE['DATA'] = $model;
        } else {
          $this->API_STATUS = 503;
          $this->API_RESPONSE['ERRORS'] = $model->errors;
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No existe'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Upload Image
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function uploadImage(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'announcement_id' => ['required', 'integer'],
      'image' => ['required', 'file']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $model = Announcement::query()->find($validator['announcement_id']);
      if ($model) {
        $imageCoverFile = $validator['image'];
        $imageCoverModel = new Image();
        $imageCoverModel->uploadImage($imageCoverFile, 'announcement');
        $imageCoverModel->tags = ['announcement'];
        $imageCoverModel->title = 'Announcement-' . $model->id;
        if ($imageCoverModel->save()) {
          $model->image_id = $imageCoverModel->id;
          if ($model->save()) {
            $this->API_RESPONSE['DATA'] = $model;
            $this->API_RESPONSE['STATUS'] = true;
          } else {
            $this->API_RESPONSE['ERRORS'] = ['Error guardando anuncio'];
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['Error guardando Imagen'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
        }
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Remove
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function remove(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'announcement_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $model = Announcement::query()->find($validator['announcement_id']);
      if ($model && $model->delete()) {
        $this->API_RESPONSE['STATUS'] = true;
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
