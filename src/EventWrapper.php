<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer\Normalizable;

/**
 * Class EventWrapper
 *
 * @package Tnc\Service\EventDispatcher
 */
class EventWrapper implements Normalizable
{
    CONST CHANNEL_PREFIX = 'event-';
    CONST EXTRA_KEY      = '_extra_';

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
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getMessageKey()
    {
        return $this->event->getMessageKey();
    }

    /**
     * @return string
     */
    public function getMessageChannel()
    {
        return $this->event->getMessageChannel();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        $data                  = $normalizer->normalize($this->event);
        $data[self::EXTRA_KEY] = [
            'class' => $this->getClass(),
            'mode'  => $this->getMode()
        ];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        $class = $mode = null;

        if (isset($data[self::EXTRA_KEY])) {
            $class = $data[self::EXTRA_KEY]['class'];
            $mode = $data[self::EXTRA_KEY]['mode'];
            unset($data[self::EXTRA_KEY]);
        }

        $this->class = (!empty($class) && class_exists($class)) ? $class : DefaultEvent::class;
        $this->mode  = $mode ?: Dispatcher::MODE_ASYNC;
        $this->event = $normalizer->denormalize($data, $this->class);
    }
}
