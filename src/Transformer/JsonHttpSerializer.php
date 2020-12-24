<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer;

use LibraryCatalog\Exception\HttpBadRequestException;
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
     * Should deserialize HTTP RAW data to array.
     *
     * @param string $data
     * @return array
     * @throws HttpBadRequestException
     */
    public function deserialize(string $data): array
    {
        try {
            $res = $this->encoder->decode($data);
        } catch (\Exception $e) {
            throw new HttpBadRequestException('Bad request, can not json-decode input data');
        }
        if (!is_array($res)) {
            throw new HttpBadRequestException('Bad request, can not json-decode input data');
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getResponseHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }
}
