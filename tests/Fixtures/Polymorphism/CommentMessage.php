<?php

namespace Ouzo\Fixtures\Polymorphism;

class CommentMessage extends Message
{
    private string $comment;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): CommentMessage
    {
        $this->comment = $comment;
        return $this;
    }
}
