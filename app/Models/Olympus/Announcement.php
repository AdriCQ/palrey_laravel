<?php

namespace App\Models\Olympus;

use App\Models\Shop\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
  use HasFactory;
  protected $table = 'ol_announcements';
  protected $guarded = ['id'];
  protected $with = ['image'];

  public static $TYPES = ['info'];

  public function image()
  {
    return $this->belongsTo(Image::class, 'image_id', 'id');
  }
}
