<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-08-28
 */

namespace mrbig00\Oblio;

use GuzzleHttp\Psr7\Response;

/**
 * Automatically convert json response to array
 *
 * @package mrbig00\Oblio
 */
class JsonAwareResponse extends Response
{
    /**
     * Cache for performance
     *
     * @var array
     */
    private $json;

    public function getBody()
    {
        if ($this->getStatusCode() != 200) {
            return parent::getBody();
        }

        if ($this->json) {
            return $this->json;
        }
        $body = parent::getBody();
        if (false !== strpos($this->getHeaderLine('Content-Type'), 'application/json')) {
            return $this->json = \json_decode($body, true);
        }
        return $body;
    }
}
