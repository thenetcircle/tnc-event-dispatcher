<?php

namespace Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Exception\FatalException;

/**
 * Serializable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Serializable
{
    /**
     * Serialize instance to textual representation.
     *
     * @param Serializer $serializer
     *
     * @return string
     */
    public function serialize(Serializer $serializer);

    /**
     * Unserialize textual representation back to this instance.
     *
     * @param string     $string
     * @param Serializer $serializer
     *
     * @return Serializable
     *
     * @throws FatalException
     */
    public function unserialize($string, Serializer $serializer);
}
