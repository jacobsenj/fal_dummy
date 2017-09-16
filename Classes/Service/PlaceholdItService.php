<?php

/**
 * PlaceholdItService
 */

namespace HDNET\FalDummy\Service;

/**
 * PlaceholdItService
 */
class PlaceholdItService extends AbstractService
{

    /**
     * Placeholder service URL.
     *
     * @var string
     */
    protected $serviceUrl = 'https://placehold.it/%dx%d&text=%s';

    /**
     * Get URL
     *
     * @param int $width
     * @param int $height
     * @param string $text
     * @return string
     */
    public function getUrl($width, $height, $text): string
    {
        return sprintf(
            $this->serviceUrl,
            $width,
            $height,
            $text
        );
    }
}
