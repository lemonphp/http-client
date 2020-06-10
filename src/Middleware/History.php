<?php

namespace Lemon\Http\Client\Middleware;

use Lemon\Http\Client\JournalInterface;
use Lemon\Http\Client\MiddlewareInterface;
use Lemon\Http\Client\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The History middleware
 *
 * Record history of request and response
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
final class History implements MiddlewareInterface
{
    /**
     * @var \Lemon\Http\Client\JournalInterface
     */
    private $journal;

    /**
     * Constructor
     *
     * @param  \Lemon\Http\Client\JournalInterface $journal
     */
    public function __construct(JournalInterface $journal)
    {
        $this->journal = $journal;
    }

    /**
     * @param  \Psr\Http\Message\RequestInterface         $request
     * @param  \Lemon\Http\Client\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startAt = \microtime(true);

        $response = $handler->handle($request);

        $duration = \microtime(true) - $startAt;

        $this->journal->record($request, $response, $duration);

        return $response;
    }
}
