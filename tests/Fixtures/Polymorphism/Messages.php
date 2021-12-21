<?php

namespace Ouzo\Fixtures\Polymorphism;

class Messages
{
    /** @var Message[] */
    private array $messages;

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): Messages
    {
        $this->messages = $messages;
        return $this;
    }
}
