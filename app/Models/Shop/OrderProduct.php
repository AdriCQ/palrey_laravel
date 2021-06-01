<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
  use HasFactory;
  public $timestamps = false;
  protected $table = 'shop_order_products';
  protected $guarded = ['id'];
  protected $casts = [
    'product_details' => 'object'
  ];

  protected $with = ['product:id,title,sell_price,production_price,wholesale_price,image_id,stock_status,stock_qty'];

  /**
   * 
   */
  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id', 'id');
  }

  public function order()
  {
    return $this->belongsTo(Order::class, 'order_id', 'id');
  }
}
