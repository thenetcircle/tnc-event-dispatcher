<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityDenormalizable;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityNormalizable;

class MockActivityStreamsEvent extends AbstractEvent implements ActivityNormalizable, ActivityDenormalizable
{
    /**
     * @var Activity
     */
    private $activity = null;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function normalizeActivity()
    {
        return $this->activity;
    }

    public function denormalizeActivity(\Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        if (null === $this->getId()) {
            $this->generateId();
        }
        if (null === $this->getVerb()) {
            $this->generateVerb();
        }
        if (null === $this->getGroup()) {
            $this->generateGroup();
        }
        $data = $serializer->normalize($this->activity);

        $extraData = [
            'name'               => $this->getName(),
            'group'              => $this->getGroup(),
            'mode'               => $this->getMode(),
            'propagationStopped' => $this->propagationStopped
        ];
        $data['extra'] = $extraData;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Serializer $serializer, array $data)
    {
        if (isset($data['extra'])) {
            $extra = $data['extra'];
            unset($data['extra']);

            if (isset($extra['name'])) {
                $this->name = $extra['name'];
            }
            if (isset($extra['group'])) {
                $this->group = $extra['group'];
            }
            if (isset($extra['mode'])) {
                $this->mode = $extra['mode'];
            }
            if (isset($extra['propagationStopped'])) {
                $this->propagationStopped = $extra['propagationStopped'];
            }
        }

        $this->activity = $serializer->denormalize($data, Activity::class);
    }

    /**
     * Generates "id" if it's not set
     */
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

    /**
     * Generates "group" if it's not set
     */
    protected function generateGroup()
    {
        $actor = $this->getActor();
        $this->setGroup(
            $actor ? ($actor->getObjectType() . '_' . $actor->getId()) : null
        );
    }

    /**
     * Generates "verb" if it's not set
     */
    protected function generateVerb()
    {
        $this->setVerb($this->getName());
    }
}