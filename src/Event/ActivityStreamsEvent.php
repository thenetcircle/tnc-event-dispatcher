<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Event\ActivityStreams\Activity;
use Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj;
use Tnc\Service\EventDispatcher\Exception\FatalException;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Normalizer\Normalizable;

/**
 * ActivityStreamsEvent
 *
 * an activity consists of an actor, a verb, an an object, and a target.
 * It tells the story of a person performing an action on or with an object --
 * "Geraldine posted a photo to her album" or "John shared a video".
 * In most cases these components will be explicit, but they may also be implied.
 *
 * @see     http://activitystrea.ms/specs/json/1.0/
 *
 * @package Tnc\Service\EventDispatcher\Event
 *
 * @author  Service Team
 *
 * @method string getId()
 * @method $this setId(string $id)
 *
 * @method string getVerb()
 * @method $this setVerb(string $verb)
 *
 * @method \Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj getActor()
 * @method $this setActor(\Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj $obj)
 *
 * @method \Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj getObject()
 * @method $this setObject(\Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj $obj)
 *
 * @method \Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj getTarget()
 * @method $this setTarget(\Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj $obj)
 *
 * @method \Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj getProvider()
 * @method $this setProvider(\Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj $obj)
 *
 * @method array getContext()
 * @method $this setContext(array $context)
 *
 * @method string getPublished()
 * @method $this setPublished(string $datetime)
 */
abstract class ActivityStreamsEvent extends AbstractEvent implements Normalizable
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * Create a new activity object
     *
     * @param string $id
     * @param string $objectType
     *
     * @return \Tnc\Service\EventDispatcher\Event\ActivityStreams\Obj
     */
    public static function createObj($objectType = null, $id = null)
    {
        return (new Obj())->setObjectType($objectType)
                          ->setId($id);
    }


    public function __construct()
    {
        $this->activity = new Activity();
        $this->activity->setPublished((new \DateTime())->format(\DateTime::RFC3339));
    }


    // Override
    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        $actor = $this->activity->getActor();
        if ($actor) {
            return $actor->getObjectType() . '_' . $actor->getId();
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getVerb();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setVerb($name);

        return $this;
    }

    //Magic methods

    public function __call($name, $arguments)
    {
        if (
            true === ($isGetter = (0 === strpos($name, 'get')))
            || 0 === strpos($name, 'set')
        ) {
            if (method_exists($this->activity, $name)) {

                $result = call_user_func_array(array($this->activity, $name), $arguments);
                return $isGetter ? $result : $this;

            } else {

                $reflClass = new \ReflectionClass($this->activity);
                foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {

                    if (0 === strpos($name, $reflMethod->name) && strlen($name) > strlen($reflMethod->name)) {

                        $attributeName = substr($name, strlen($reflMethod->name));

                        if ($isGetter) {

                            $obj = $reflMethod->invoke($this->activity);
                            if (!$obj instanceof Obj) {
                                return null;
                            } else {
                                return call_user_func_array(array($obj, 'get' . $attributeName), $arguments);
                            }

                        } else {

                            $obj          = null;
                            $getterMethod = 'get' . substr($reflMethod->name, 3);
                            if ($reflClass->hasMethod($getterMethod)) {
                                $obj = call_user_func(array($this->activity, $getterMethod));
                            }

                            if (null === $obj || !$obj instanceof Obj) {
                                return null;
                            }

                            call_user_func_array(array($obj, 'set' . $attributeName), $arguments);
                            $reflMethod->invoke($this->activity, $obj);
                            return $this;

                        }

                    }

                }

            }
        }

        throw new FatalException(
            sprintf('Method %s does not exists in %s.', $name, __CLASS__)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        if (null === $this->getId()) {
            $this->generateId();
        }

        return $serializer->normalize($this->activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Serializer $serializer, array $data)
    {
        $this->activity = $serializer->denormalize($data, Activity::class);
    }

    protected function generateId()
    {
        $uuidArr = [$this->getProvider()->getId()];
        if ($this->getActor()->getId() !== null) {
            $uuidArr[] = $this->getActor()->getObjectType();
            $uuidArr[] = $this->getActor()->getId();
        }
        $uuidArr[] = time();
        $uuidArr[] = sprintf('%03d', mt_rand(0, 999));
        $uuid      = implode('-', $uuidArr);

        $this->setId($uuid);
    }
}