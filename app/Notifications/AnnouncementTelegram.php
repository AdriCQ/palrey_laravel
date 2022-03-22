<?php

namespace App\Notifications;

use App\Models\Shop\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;

/**
 * AnnouncementTelegram
 */
class AnnouncementTelegram extends Notification implements ShouldQueue
{
    use Queueable;

    public Product $product;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }


    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        Log::info($notifiable);
        $chatId = $notifiable;
        if (isset($notifiable->telegram_chat_id)) {
            $chatId = $notifiable->telegram_chat_id;
        } else if (isset($notifiable['telegram_chat_id'])) {
            $chatId = $notifiable['telegram_chat_id'];
        }
        return TelegramFile::create()
            // Optional recipient user id.
            ->to((int)$chatId)
            ->photo(public_path($this->product->image->paths['sm']))
            // Markdown supported.
            ->content("\xF0\x9F\x91\x91\xF0\x9F\x91\x91\xF0\x9F\x91\x91 PalRey \xF0\x9F\x91\x91\xF0\x9F\x91\x91\xF0\x9F\x91\x91 \n
*" . $this->product->title . "*
\n" . $this->product->description . "
\n\xF0\x9F\x8D\x95 Pizzas Familiares
\xF0\x9F\x8E\x82 Panetelas
\xF0\x9F\x8D\xA9 Dulces variados
\xF0\x9F\x8D\xB9 Jugos
\n\xF0\x9F\x9A\xB4 Con envio a domicilio!!!
\n\xF0\x9F\x93\xB2 Disponible en nuestra aplicacion (https://palrey.nairda.net/Palrey.apk)
\n\xF0\x9F\x93\xB2 Para iPhone (https://palrey.nairda.net)
\n\xF0\x9F\x8F\xA0No esperes mas y disfruta de nuestros servicios desde tu hogar \xF0\x9F\x8F\xA0");
    }
}
