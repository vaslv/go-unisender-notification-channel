<?php

namespace NotificationChannels\GoUnisender;

use Illuminate\Notifications\Notification;

class GoUnisenderChannel {
    /**
     * @var GoUnisenderApi
     */
    protected $api;

    /**
     * GoUnisenderChannel constructor.
     *
     * @param GoUnisenderApi $api
     */
    public function __construct(GoUnisenderApi $api) {
        $this->api = $api;
    }

    /**
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array|null
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification): ?array {
        $to = $notifiable->routeNotificationFor('goUnisender');

        if (!$to) {
            throw new \InvalidArgumentException('No receivers.');
        }

        if (!method_exists($notification, 'toGoUnisender')) {
            throw new \InvalidArgumentException('Method "toGoUnisender" does not exists on given notification instance.');
        }

        /** @var GoUnisenderMessage $message */
        $message = $notification->toGoUnisender($notifiable);

        if (!($message instanceof GoUnisenderMessage)) {
            throw new \InvalidArgumentException('Message is not an instance of GoUnisenderMessage.');
        }

        $message->to($to);

        return $this->sendMessage($message);
    }

    /**
     * @param \NotificationChannels\GoUnisender\GoUnisenderMessage $message
     * @return array|null
     * @throws \Exception
     */
    protected function sendMessage(GoUnisenderMessage $message): ?array {

        try {
            return $this->api->sendEmail($message);
        } catch (\Exception $e) {
          throw $e;
        }

        return NULL;
    }
}
