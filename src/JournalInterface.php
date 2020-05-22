<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The journal interface
 *
 * @package     Lemon\Http\Client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
interface JournalInterface
{
    /**
     * Records an entry in the journal.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The request
     * @param  \Psr\Http\Message\ResponseInterface $response The response
     * @param  float|null                          $duration The duration in seconds
     */
    public function record(RequestInterface $request, ResponseInterface $response, float $duration = null): void;

    /**
     * Get last sent request
     *
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getLastRequest(): ?RequestInterface;

    /**
     * Get last received response
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getLastResponse(): ?ResponseInterface;
}
