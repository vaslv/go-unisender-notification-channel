<?php

namespace NotificationChannels\GoUnisender;

class GoUnisenderMessage {
  public $to;
  public $template;
  public $substitutions;

  /**
   * Set the message's receivers.
   *
   * @param array|string $to
   *
   * @return GoUnisenderMessage
   */
  public function to(string $to): GoUnisenderMessage {
    $this->to = $to;

    return $this;
  }

  public function template(string $template): GoUnisenderMessage {
    $this->template = $template;

    return $this;
  }

  public function substitutions(array $substitutions): GoUnisenderMessage {
    $this->substitutions = $substitutions;

    return $this;
  }
}
