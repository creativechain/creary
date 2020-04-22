<?php

namespace App\Notifications;

use App\Broadcasting\DatabaseChannel;
use App\Broadcasting\MqttChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CrearyNotification extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [MqttChannel::class, 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return (array) $this->data;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable) {
        return $this->toArray($notifiable);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toMqtt($notifiable) {
        return $this->toArray($notifiable);
    }
}
