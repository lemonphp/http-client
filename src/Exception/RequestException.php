<?php

namespace Lemon\Http\Client\Exception;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * The request exception
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class RequestException extends RuntimeException implements RequestExceptionInterface
{
    /**
     * Request exception constructor
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  string $message
     * @param  int $code
     * @param  \Throwable|null $previous
     */
    public function __construct(RequestInterface $request, $message = '', $code = 0, ?Throwable $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
