<?php

namespace App\Notifications\User;

use App\Models\Olympus\Comment as OlympusComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

use App\Models\User;
// use Illuminate\Notifications\Messages\BroadcastMessage;

class Comment extends Notification implements ShouldQueue
{
  use Queueable;

  public OlympusComment $comment;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(OlympusComment $comment)
  {
    $this->comment = $comment;
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
      // ->content('Nueva Orden')
      // Markdown supported.
      ->view('notification.user.comment', ['comment' => $this->comment, 'user' => $notifiable]);
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
      'comment_id' => $this->comment->id,
      'user_id' => $notifiable->id
    ];
  }
}
