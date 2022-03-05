<?php

namespace App\Notifications\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\Shop\Order as ShopOrder;
use App\Models\User;
// use Illuminate\Notifications\Messages\BroadcastMessage;

class Order extends Notification implements ShouldQueue
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


  public function via($notifiable)
  {
    return [TelegramChannel::class, 'database'];
  }

  public function toTelegram($notifiable)
  {
    return TelegramMessage::create()
      // Optional recipient user id.
      ->to(env('TELEGRAM_CHAT_ID', '913493292'))
      // ->content('Nueva Orden')
      // Markdown supported.
      ->view('notification.shop.order', ['order' => $this->order]);
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray(User $notifiable)
  {
    return [
      'order_id' => $this->order->id,
      'user_id' => $notifiable->id
    ];
  }
}
