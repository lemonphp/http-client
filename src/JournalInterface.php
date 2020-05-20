<?php

namespace Lemon\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
}
