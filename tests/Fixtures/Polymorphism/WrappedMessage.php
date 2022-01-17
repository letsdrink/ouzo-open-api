<?php

namespace Ouzo\Fixtures\Polymorphism;

class WrappedMessage
{
    private Message $message;

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): WrappedMessage
    {
        $this->message = $message;
        return $this;
    }
}
