<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer;

use LibraryCatalog\Transformer\Encoder\EncoderInterface;

class JsonHttpSerializer implements HttpSerializerInterface
{
    /** @var EncoderInterface */
    protected $encoder;

    /**
     * Serializer constructor.
     * @param EncoderInterface $encoder
     */
    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param $data
     * @return string
     * @throws Encoder\Exception
     */
    public function serialize($data): string
    {
        return $this->encoder->encode($data);
    }

    /**
     * @return array
     */
    public function getResponseHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }
}
