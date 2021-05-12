<?php

namespace App\Http\Controllers\Olympus;

use App\Http\Controllers\Controller;
use App\Models\User;
// use Illuminate\Http\Request;

class NotificationController extends Controller
{
  /**
   * Get unread notifications
   * @return Illuminate\Http\JsonResponse
   */
  public function getUnread()
  {
    $this->API_RESPONSE['DATA'] = auth()->user()->unreadNotifications;
    $this->API_RESPONSE['STATUS'] = true;
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * 
   */
  public function aGetUnread()
  {
    $notifications = User::first()->unreadNotifications;
    $data = [];
    foreach ($notifications as $n) {
      if ($n->type === 'App\\Notifications\\Shop\\NewOrderNotification') {
        array_push($data, 'Nueva orden!!!');
        $n->markAsRead();
      }
    }
    $this->API_RESPONSE['DATA'] = $data;
    $this->API_RESPONSE['STATUS'] = true;
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
