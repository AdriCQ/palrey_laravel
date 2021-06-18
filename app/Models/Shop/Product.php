<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $table = 'shop_products';
  protected $guarded = ['id'];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'tags' => 'array',
    'attributes' => 'object'
  ];

  protected $with = ['image:id,title,paths', 'owner:id,name,mobile_phone'];

  public static $STOCK_STATUS = ['limited', 'infinity', 'backorder', 'sold_out'];

  public static $PAGINATE = 64;

  /**
   * 
   */
  public static function tableFields($extraFields = [])
  {
    $fields = ['id', 'title', 'description', 'sell_price', 'stock_status', 'stock_qty', 'rating_average', 'image_id', 'owner_id', 'sold', 'attributes'];
    if (count($extraFields))
      $fields = array_merge($fields, $extraFields);
    return $fields;
  }

  /**
   * 
   */
  public function owner()
  {
    return $this->belongsTo(User::class, 'owner_id', 'id');
  }
  /**
   * 
   */
  public function image()
  {
    return $this->belongsTo(Image::class, 'image_id', 'id');
  }
  /**
   * 
   */
  public function category()
  {
    return $this->belongsTo(Category::class, 'category_id', 'id');;
  }
}
