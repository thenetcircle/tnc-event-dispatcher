<?php

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

/**
 * Class WrappedEvent
 *
 * @package Tnc\Service\EventDispatcher
 */
class WrappedEvent implements Normalizable
{
    /**
     * @var BaseEvent
     */
    protected $event;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $group;
    /**
     * @var string
     */
    protected $mode;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var int
     */
    protected $time;


    /**
     * @param string    $name
     * @param BaseEvent $event
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, BaseEvent $event, $group, $mode)
    {
        if (!$event instanceof Normalizable) {
            throw new InvalidArgumentException(
                sprintf('{WrappedEvent} Event %s was not an instance of Normalizable', get_class($event))
            );
        }

        $this->event = $event;
        $this->name  = $name;
        $this->group = $group;
        $this->mode  = $mode;
        $this->class = get_class($event);
        $this->time  = time();
    }

    /**
     * @return BaseEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $data         = get_object_vars($this);
        unset($data['event']);
        $data['data'] = $serializer->normalize($this->event);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        if (!isset($data['class'], $data['data'])) {
            throw new InvalidArgumentException(sprintf('{WrappedEvent} some arguments missed in data %s', json_encode($data)));
        }

        $this->event = $serializer->denormalize($data['class'], $data['data']);
        unset($data['data']);

        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}
