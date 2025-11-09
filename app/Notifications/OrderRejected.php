<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail', \App\Notifications\Channels\WebPushChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('❌ Commande #' . $this->order->id . ' rejetée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre commande #' . $this->order->id . ' a été rejetée par la mercerie.')
            ->line('Montant : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->action('Voir les détails', route('orders.show', $this->order->id))
            ->line('Nous vous invitons à contacter la mercerie pour plus d\'informations.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Votre commande #' . $this->order->id . ' a été rejetée',
            'mercerie_name' => $this->order->mercerie->name,
            'url' => route('orders.show', $this->order->id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'order_id' => $this->order->id,
            'message' => 'Votre commande #' . $this->order->id . ' a été rejetée',
            'url' => route('orders.show', $this->order->id),
        ]);
    }

    public function toWebPush($notifiable)
    {
        return [
            'title' => '❌ Commande rejetée #' . $this->order->id,
            'body' => 'Votre commande a été rejetée par la mercerie.',
            'url' => route('orders.show', $this->order->id),
            'icon' => null,
            'data' => ['order_id' => $this->order->id],
        ];
    }
}