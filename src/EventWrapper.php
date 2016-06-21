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
    CONST CHANNEL_PREFIX = 'event-';
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
     * @var string
     */
    protected $mode;


    /**
     * @param Event  $event
     * @param string $mode
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Event $event, $mode)
    {
        if (!$event instanceof Normalizable) {
            throw new InvalidArgumentException(
                sprintf('{EventWrapper} Event %s was not an instance of Normalizable', get_class($event))
            );
        }

        $this->event = $event;
        $this->class = get_class($event);
        $this->mode  = $mode;
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
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        // TODO
        return null;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        $name = $this->event->getName();
        if (($pos = strpos($name, '.')) !== false) {
            $channel = substr($name, 0, $pos);
        } else {
            $channel = $name;
        }

        return self::CHANNEL_PREFIX . $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $data                  = $serializer->normalize($this->event);
        $data[self::EXTRA_KEY] = [
            'class' => $this->getClass(),
            'mode'  => $this->getMode()
        ];
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
        $this->class = $data[self::EXTRA_KEY]['class'];
        $this->mode  = $data[self::EXTRA_KEY]['mode'];
        unset($data[self::EXTRA_KEY]);

        $this->event = $serializer->denormalize($this->class, $data);
    }
}
