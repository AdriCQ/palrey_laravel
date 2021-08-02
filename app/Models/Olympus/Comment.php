<?php

namespace App\Models\Olympus;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
  use HasFactory;
  protected $table = 'ol_comments';
  protected $guarded = ['id'];

  public static $SUBJECTS = ['Sugerencia', 'Queja', 'Solicitud', 'Agradecimiento'];
  public static $MAX_PER_DAY = 5;

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
