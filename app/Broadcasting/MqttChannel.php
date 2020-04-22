<?php

namespace App\Broadcasting;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Salman\Mqtt\MqttClass\Mqtt;

class MqttChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toMqtt($notifiable);

        // Send notification to the $notifiable instance...

        $mqtt = new Mqtt();
        $to = $message['to'];
        $mqtt->ConnectAndPublish("$to/notification", json_encode($message));

    }
}
