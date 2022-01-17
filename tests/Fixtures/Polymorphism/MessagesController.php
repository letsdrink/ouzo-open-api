<?php

namespace Ouzo\Fixtures\Polymorphism;

class MessagesController
{
    public function singleMessage(): Message
    {
        return new DirectMessage();
    }

    public function multipleMessages(): Messages
    {
        return new Messages();
    }

    public function wrappedSingleMessage(): WrappedMessage
    {
        return new WrappedMessage();
    }
}
