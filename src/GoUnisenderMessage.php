<?php

namespace NotificationChannels\GoUnisender;

class GoUnisenderMessage {
    public $to;
    public $from;
    public $content;
    public $token;
    public $silent = FALSE;

    /**
     * Set API Token.
     *
     * @param string $token
     *
     * @return GoUnisenderMessage
     */
    public function usingApiToken(string $token): GoUnisenderMessage {
        $this->token = $token;

        return $this;
    }

    /**
     * Send message silently (without raising any exceptions).
     *
     * @param bool $flag
     *
     * @return GoUnisenderMessage
     */
    public function silent(bool $flag = TRUE): GoUnisenderMessage {
        $this->silent = $flag;

        return $this;
    }

    /**
     * Set the message's receivers.
     *
     * @param array|string $to
     *
     * @return GoUnisenderMessage
     */
    public function to($to): GoUnisenderMessage {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $this->to = $to;

        return $this;
    }

    /**
     * Set the message's sender.
     *
     * @param string $from
     *
     * @return GoUnisenderMessage
     */
    public function from(string $from): GoUnisenderMessage {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the message's content.
     *
     * @param string $content
     *
     * @return GoUnisenderMessage
     */
    public function content(string $content): GoUnisenderMessage {
        $this->content = $content;

        return $this;
    }
}
