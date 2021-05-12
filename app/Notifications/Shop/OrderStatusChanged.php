<?php

namespace App\Notifications\Shop;

use App\Models\Shop\Order as ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
  use Queueable;
  public ShopOrder $order;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(ShopOrder $order)
  {
    $this->order = $order;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array
   */
  public function via()
  {
    return ['databse', 'broadcast'];
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array
   */
  public function toArray()
  {
    return [
      'order_id' => $this->order->id
    ];
  }

  /**
   * Get the type of the notification being broadcast.
   *
   * @return string
   */
  public function broadcastType()
  {
    return 'shop.order';
  }
}
