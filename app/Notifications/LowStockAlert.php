<?php

namespace App\Notifications;

use App\Models\MerchantSupply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MerchantSupply $merchantSupply)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Stock faible pour ' . $this->merchantSupply->supply->name)
            ->greeting('Alerte stock faible')
            ->line('Votre stock de "' . $this->merchantSupply->supply->name . '" est faible.')
            ->line('Stock actuel : ' . $this->merchantSupply->stock_quantity)
            ->action('Gérer le stock', route('merchant.supplies.index'))
            ->line('Pensez à réapprovisionner rapidement.');
    }

    public function toArray($notifiable)
    {
        return [
            'supply_id' => $this->merchantSupply->supply_id,
            'supply_name' => $this->merchantSupply->supply->name,
            'current_stock' => $this->merchantSupply->stock_quantity,
            'message' => 'Stock faible pour ' . $this->merchantSupply->supply->name,
            'url' => route('merchant.supplies.index'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'supply_id' => $this->merchantSupply->supply_id,
            'supply_name' => $this->merchantSupply->supply->name,
            'current_stock' => $this->merchantSupply->stock_quantity,
            'message' => 'Stock faible pour ' . $this->merchantSupply->supply->name,
            'url' => route('merchant.supplies.index'),
        ]);
    }
}