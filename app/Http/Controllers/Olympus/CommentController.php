<?php

namespace App\Http\Controllers\Olympus;

use App\Http\Controllers\Controller;
use App\Models\Olympus\Comment;
use App\Notifications\User\Comment as UserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
  /**
   * Create
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'subject' => ['required', 'in:' . implode(',', Comment::$SUBJECTS)],
      'message' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      if (auth()->user()->comments()->whereDate('created_at', now()->toDateTimeString())->count() < Comment::$MAX_PER_DAY) {
        $comment = new Comment([
          'subject' => $validator['subject'],
          'message' => $validator['message'],
          'user_id' => auth()->user()->id
        ]);
        if ($comment->save()) {
          $this->API_RESPONSE['DATA'] = $comment;
          $this->API_RESPONSE['STATUS'] = true;
          // Send Notificaiton
          Notification::send(auth()->user(), new UserComment($comment));
        } else {
          $this->API_RESPONSE['ERRORS'] = $comment->errors;
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['Ha alcanzado el limite de comentarios diario'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
