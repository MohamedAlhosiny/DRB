<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderstausUpdated extends Notification
{
    use Queueable;


   protected $orderID;
   protected $oldStatus;
   protected $newStatus;
   protected $userName;

    public function __construct($orderID , $oldStatus , $newStatus , $userName)
    {
        $this->orderID = $orderID;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail' , 'database'];
    }

public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('تم تعديل حالة الطلب رقم ' . $this->orderID)
        ->greeting('مرحبا ' . $this->userName)
        ->line('تم تعديل حالة طلبك رقم ' . $this->orderID . ' من حالة ' . $this->oldStatus . ' إلى حالة ' . $this->newStatus);
}


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderID,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'user_name' => $this->userName,
            'message' => 'order status updated successfully'
        ];
    }
}
