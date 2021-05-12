<?php

namespace App\Models\Olympus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class App extends Model
{
  use HasFactory;
  protected $table = 'ol_apps';
  protected $guarded = ['id'];
  protected $hidden = ['token'];
  protected $casts = ['roadmap' => 'object', 'settings' => 'object'];

  public function generateToken()
  {
    return $this->id . Hash::make($this->token);
  }

  /**
   * Check app Token
   */
  public static function checkToken($queryToken)
  {
    if ($queryToken) {
      $queryToken = explode('|', $queryToken);
      if (is_numeric($queryToken[0])) {
        $app = self::query()->find($queryToken[0], ['id', 'token']);
        if ($app) {
          if (Hash::check($app->token, $queryToken[1])) {
            return true;
          }
        }
      }
    }
    return false;
  }

  /**
   * Check app updates
   */
  public static function checkForUpdates($queryToken, $version = 0)
  {
    if ($queryToken) {
      $queryToken = explode('|', $queryToken);
      if (is_numeric($queryToken[0])) {
        $app = self::query()->find($queryToken[0]);
        if ($app && $app->version > $version) {
          return $app;
        }
      }
    }
    return null;
  }

  /**
   * 
   */
  public static function getByToken($queryToken, $columns = ['title', 'version', 'daily_visits', 'roadmap', 'updated_at'])
  {
    if ($queryToken) {
      $queryToken = explode('|', $queryToken);
      if (is_numeric($queryToken[0])) {
        // array_push($columns, 'id', 'token');
        $app = self::query()->find($queryToken[0]);
        if ($app) {
          if (Hash::check($app->token, $queryToken[1])) {
            return $app;
          }
        }
      }
    }
    return null;
  }

  /**
   * Download App
   */
  public function download(string $type = 'apk')
  {
    if (Storage::exists('public/olympus/apps/' . $this->token . '.' . $type))
      return Storage::download('public/olympus/apps/' . $this->token . '.' . $type, $this->title . '-v' . $this->version . '.' . $type);
  }

  public function downloadUrl(string $type = "apk")
  {
    if (Storage::exists('public/olympus/apps/' . $this->token . '.' . $type))
      return Storage::url('public/olympus/apps/' . $this->token . '.' . $type);
  }
}
