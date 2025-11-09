<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderReceived extends Notification implements ShouldQueue
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
            ->subject('🛒 Nouvelle commande #' . $this->order->id . ' reçue !')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Vous avez reçu une nouvelle commande de ' . $this->order->couturier->name . '.')
            ->line('Montant total : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('Nombre d\'articles : ' . $this->order->items->count())
            ->action('Gérer la commande', route('orders.index'))
            ->line('Merci de traiter cette commande dans les plus brefs délais.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Nouvelle commande #' . $this->order->id . ' de ' . $this->order->couturier->name,
            'amount' => $this->order->total_amount,
            'couturier_name' => $this->order->couturier->name,
            'couturier_avatar' => $this->order->couturier->getAvatarUrlAttribute() ?? null,
            'items_count' => $this->order->items->count(),
            'url' => route('orders.show', $this->order->id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'order_id' => $this->order->id,
            'message' => 'Nouvelle commande #' . $this->order->id . ' de ' . $this->order->couturier->name,
            'url' => route('orders.show', $this->order->id),
            'couturier_avatar' => $this->order->couturier->getAvatarUrlAttribute() ?? null,
        ]);
    }

    /**
     * Payload for WebPush channel
     */
    public function toWebPush($notifiable)
    {
        return [
            'title' => '🛒 Nouvelle commande #' . $this->order->id,
            'body' => 'Commande reçue de ' . $this->order->couturier->name,
            'url' => route('orders.show', $this->order->id),
            'icon' => null,
            'data' => ['order_id' => $this->order->id],
        ];
    }
}