<?php

namespace App\Models\Olympus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
  use HasFactory;
  protected $table = 'ol_announcements';
  protected $guarded = ['id'];

  public static $TYPES = ['info'];
}
