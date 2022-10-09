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
   * @return void
   * @throws \Exception
   */
  public function send($notifiable, Notification $notification): void {
    if (!method_exists($notification, 'toGoUnisender')) {
      throw new \InvalidArgumentException('Method "toGoUnisender" does not exists on given notification instance.');
    }

    /** @var GoUnisenderMessage $message */
    $message = $notification->toGoUnisender($notifiable);

    if (!($message instanceof GoUnisenderMessage)) {
      throw new \InvalidArgumentException('Message is not an instance of GoUnisenderMessage.');
    }

    if (empty($message->to)) {
      $to = $notifiable->routeNotificationFor('goUnisender');

      if (empty($to)) {
        throw new \InvalidArgumentException('No receivers.');
      }

      $message->setTo($to);
    }

    $this->sendMessage($message);
  }

  /**
   * @param \NotificationChannels\GoUnisender\GoUnisenderMessage $message
   * @return void
   * @throws \Exception
   */
  protected function sendMessage(GoUnisenderMessage $message): void {
    $this->api->sendEmail($message);
  }
}
