<?php

namespace Ouzo\Fixtures\Polymorphism;

class DirectMessage extends Message
{
    private int $userId;
    private string $body;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): DirectMessage
    {
        $this->userId = $userId;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): DirectMessage
    {
        $this->body = $body;
        return $this;
    }
}
