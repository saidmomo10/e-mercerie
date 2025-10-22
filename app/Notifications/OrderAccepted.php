<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎉 Commande #' . $this->order->id . ' acceptée !')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Votre commande #' . $this->order->id . ' a été acceptée par la mercerie.')
            ->line('Montant total : ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->action('Voir la commande', route('orders.show', $this->order->id))
            ->line('Merci pour votre confiance !');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Votre commande #' . $this->order->id . ' a été acceptée',
            'amount' => $this->order->total_amount,
            'mercerie_name' => $this->order->mercerie->name,
            'url' => route('orders.show', $this->order->id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'message' => 'Votre commande #' . $this->order->id . ' a été acceptée',
            'url' => route('orders.show', $this->order->id),
        ]);
    }
}