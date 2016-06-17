<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

/**
 * Class EventWrapper
 *
 * @package Tnc\Service\EventDispatcher
 */
class EventWrapper implements Normalizable
{
    CONST EXTRA_KEY = '_extra_';

    /**
     * @var Event
     */
    protected $event;
    /**
     * @var string
     */
    protected $class;


    /**
     * @param Event $event
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Event $event)
    {
        if (!$event instanceof Normalizable) {
            throw new InvalidArgumentException(
                sprintf('{EventWrapper} Event %s was not an instance of Normalizable', get_class($event))
            );
        }

        $this->event = $event;
        $this->class = get_class($event);
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $data                  = $serializer->normalize($this->event);
        $data[self::EXTRA_KEY] = [
            'class' => $this->getClass(),
            'mode'  => $data['mode'],
            'group' => $data['group'],
        ];
        unset($data['mode'], $data['group']);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        if (!isset($data[self::EXTRA_KEY]['class'])) {
            throw new InvalidArgumentException(sprintf('{EventWrapper} some arguments missed in data %s', json_encode($data)));
        }
        $extraInfo     = $data[self::EXTRA_KEY];
        $this->class   = $extraInfo['class'];
        $data['mode']  = $extraInfo['mode'];
        $data['group'] = $extraInfo['group'];
        unset($data[self::EXTRA_KEY]);

        $this->event = $serializer->denormalize($this->class, $data);
    }
}
