<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;
  protected $table = 'shop_orders';
  protected $guarded = ['id'];
  protected $with = ['customer:id,name,mobile_phone', 'products'];
  protected $casts = [
    'coordinates' => 'object'
  ];
  public static $STATUS = ['processing', 'accepted', 'v-canceled', 'c-canceled', 'completed'];

  public function resetProductsQty()
  {
    foreach ($this->products as $orderProduct) {
      $product = $orderProduct->product;
      if ($product->stock_status === 'sold_out')
        $product->stock_status = 'limited';
      if ($product->stock_status === 'limited') {
        $product->stock_qty += $orderProduct->product_qty;
        $product->sold -= $orderProduct->product_qty;
        if (!$product->save())
          return false;
      }
    }
    return true;
  }

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */


  public function products()
  {
    return $this->hasMany(OrderProduct::class, 'order_id', 'id');
  }
  /**
   * 
   */
  public function customer()
  {
    return $this->belongsTo(User::class, 'customer_id', 'id');
  }
}
