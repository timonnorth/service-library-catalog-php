<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer\Encoder;

interface EncoderInterface
{
    /**
     * @param mixed $data
     * @throws Exception
     * @return string
     */
    public function encode($data): string;

    /**
     * @param string $value
     * @throws Exception
     * @return mixed
     */
    public function decode(string $value);
}
