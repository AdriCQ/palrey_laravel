<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Register extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  public function via($notifiable)
  {
    return [TelegramChannel::class, 'database'];
  }

  public function toTelegram($notifiable)
  {
    return TelegramMessage::create()
      // Optional recipient user id.
      ->to(-1001151880402)
      // Markdown supported.
      ->view('notification.user.registered', ['user' => $notifiable]);
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->line('The introduction to the notification.')
      ->action('Notification Action', url('/'))
      ->line('Thank you for using our application!');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
