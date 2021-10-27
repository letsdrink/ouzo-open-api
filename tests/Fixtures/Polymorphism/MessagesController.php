<?php

namespace Ouzo\Fixtures\Polymorphism;

class MessagesController
{
    public function singleMessage(): Message
    {
        return new DirectMessage();
    }
}
