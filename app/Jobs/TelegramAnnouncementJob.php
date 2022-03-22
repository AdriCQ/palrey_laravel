<?php

namespace App\Jobs;

use App\Models\Shop\Product;
use App\Notifications\AnnouncementTelegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class TelegramAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $chats;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifiable)
    {
        $this->chats = $notifiable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = Product::query()->where('onsale', true)->inRandomOrder()->first();
        Notification::send($this->chats, new AnnouncementTelegram($product));
    }
}
