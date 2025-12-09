<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Invoice $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Factuur betaald - Actie vereist')
                    ->greeting('Hallo ' . $notifiable->name . ',')
                    ->line('Een factuur is betaald en vereist uw aandacht.')
                    ->line('Klant: ' . $this->invoice->company->name)
                    ->line('Factuurnummer: ' . $this->invoice->invoice_number)
                    ->line('Bedrag: € ' . number_format($this->invoice->total_amount, 2, ',', '.'))
                    ->line('Betaald op: ' . $this->invoice->paid_at->format('d-m-Y H:i'))
                    ->action('Bekijk factuur', url('/purchase/paid-invoices/' . $this->invoice->id))
                    ->line('Producten op deze factuur moeten nu worden besteld en geleverd.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'company_name' => $this->invoice->company->name,
            'total_amount' => $this->invoice->total_amount,
            'paid_at' => $this->invoice->paid_at,
            'products' => $this->invoice->lines->map(function ($line) {
                return [
                    'product_name' => $line->product->name,
                    'amount' => $line->amount,
                    'delivery_status' => $line->delivery_status,
                ];
            }),
        ];
    }
}
