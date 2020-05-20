<?php

namespace Lemon\Http\Client\History;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Entry
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * The duration in seconds
     *
     * @var float|null
     */
    private $duration;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @param  float $duration The duration in seconds
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, float $duration = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->duration = $duration;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return float|null
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }
}
