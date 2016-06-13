<?php

namespace Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentsException;

/**
 * JsonSerializable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
trait JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Serializer $serializer)
    {
        return json_encode(get_object_vars($this));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string, Serializer $serializer)
    {
        if (null === ($data = json_decode($string, true))) {
            throw new InvalidArgumentsException(sprintf('{%s} can not unserialize data %s', get_called_class(), $string));
        }
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}
