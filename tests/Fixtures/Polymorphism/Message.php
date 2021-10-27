<?php

namespace Ouzo\Fixtures\Polymorphism;

use Ouzo\OpenApi\Attribute\Schema;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: 'messageType',
    mapping: [
        MessageType::COMMENT => CommentMessage::class,
        MessageType::DIRECT => DirectMessage::class,
    ]
)]
abstract class Message
{
    #[Schema(required: true)]
    private string $messageType;

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function setMessageType(string $messageType): Message
    {
        $this->messageType = $messageType;
        return $this;
    }
}
