<?php

declare(strict_types=1);

namespace Timonnorth\ServiceLibraryCatalogPhp;

class SkeletonClass
{
    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        // constructor body
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     * @return string
     */
    public function echoPhrase(string $phrase): string
    {
        return $phrase;
    }
}