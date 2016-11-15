<?php

namespace Tnc\Service\EventDispatcher\Test\Event;

use Tnc\Service\EventDispatcher\Event\ActivityEvent;

class ActivityStreamsEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterSetter()
    {
        $context = [
            'ip'         => '192.168.1.1',
            'user-agent' => 'test-agent'
        ];

        $message = new Message();

        $event = new MessageEvent($message, '111', '222', $context);

        $this->assertEquals($event->getActorId(), '111');
        $this->assertEquals($event->getActorObjectType(), 'user');
        $this->assertEquals($event->getActorDisplayName(), 'benn');
        $this->assertEquals($event->getActor(), MessageEvent::obj('user', '111')->setDisplayName('benn'));

        $this->assertEquals($event->getTargetId(), '222');
        $this->assertEquals($event->getTargetObjectType(), 'user');
        $this->assertEquals($event->getTarget(), MessageEvent::obj('user', '222'));

        $this->assertEquals($event->getContext(), $context);

        $this->assertEquals($event->getObjectId(), $message->getId());
        $this->assertEquals($event->getObjectObjectType(), 'message');
        $this->assertEquals($event->getObjectContent(), $message->getMessage());
        $this->assertEquals(
            $event->getObject(),
            MessageEvent::obj('message', $message->getId())->setContent($message->getMessage())
        );
    }
}

class MessageEvent extends ActivityEvent
{
    const SEND = 'message.send';

    public function __construct(Message $message, $senderId, $receiverId, $context)
    {
        parent::__construct();

        $this->setActorId($senderId)
             ->setActorObjectType('user')
             ->setActorDisplayName('benn')
             ->setTargetId($receiverId)
             ->setTargetObjectType('user')
             ->setObjectId($message->getId())
             ->setObjectObjectType('user')
             ->setContext($context);

        $this->setObject(
            self::obj('message', $message->getId())
                ->setContent($message->getMessage())
        );
    }
}

class Message
{
    public function getId()
    {
        return '333';
    }

    public function getMessage()
    {
        return 'Im a message';
    }
}