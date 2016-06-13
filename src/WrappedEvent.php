<?php

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Serializer\Serializable;
use Tnc\Service\EventDispatcher\Serializer\Serializer;

/**
 * Class WrappedEvent
 *
 * @package Tnc\Service\EventDispatcher
 */
class WrappedEvent implements Serializable
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
     * @throws FatalException
     */
    public function __construct($name, BaseEvent $event, $group, $mode)
    {
        if (!$event instanceof Serializable) {
            throw new FatalException(
                sprintf('{WrappedEvent} Event %s was not an instance of Serializable', get_class($event))
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
    public function serialize(Serializer $serializer)
    {
        $data          = get_object_vars($this);
        $data['event'] = $serializer->serialize($this->event);
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string, Serializer $serializer)
    {
        $data = json_decode($string, true, 1);
        if(!isset($data['event'], $data['class'], $data['name'])) {
            throw new FatalException(sprintf('{WrappedEvent} can not unserialize data %s', $string));
        }
        $data['event'] = $serializer->unserialize($data['class'], $data['event']);

        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}
