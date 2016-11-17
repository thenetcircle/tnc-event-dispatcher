<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreamsNormalizer;
use Tnc\Service\EventDispatcher\Normalizer\CustomNormalizer;

/**
 * DefaultSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
class DefaultSerializer extends AbstractSerializer
{
    /**
     * AbstractSerializer constructor.
     *
     * @param \Tnc\Service\EventDispatcher\Interfaces\Normalizer[] $normalizers
     */
    public function __construct(array $normalizers = null)
    {
        if ($normalizers === null) {
            $normalizers = [new ActivityStreamsNormalizer(), new CustomNormalizer()];
        }

        parent::__construct($normalizers);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($content)
    {
        if (null === ($data = json_decode($content, true))) {
            throw new InvalidArgumentException(sprintf('String %s can not be decoded.', $content));
        }

        return $data;
    }
}
