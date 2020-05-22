<?php

namespace Lemon\Http\Client\Transport;

use Lemon\Http\Client\Exception\NetworkException;
use Lemon\Http\Client\Helper;
use Lemon\Http\Client\TransportInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The CURL transport
 *
 * @package     Lemon\Http\Client\Transport
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class CurlTransport implements TransportInterface
{
    use OptionsAwareTransport;

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \UnexpectedValueException
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $resource = \fopen('php://temp', 'wb');

        $curlOptions = [
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => $this->options['follow_location'],
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => $this->options['timeout'],
            CURLOPT_FILE => $resource,
        ];

        $version = $request->getProtocolVersion();
        switch ($version) {
            case "1.0":
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
                break;

            case "1.1":
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;

            case "2.0":
                if (!\defined('CURL_HTTP_VERSION_2_0')) {
                    throw new \UnexpectedValueException('libcurl 7.33 needed for HTTP 2.0 support');
                }

                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
                break;

            default:
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;
        }

        $curlOptions[CURLOPT_HTTPHEADER] = \explode(
            "\r\n",
            Helper::serializeHeadersFromPsr7Format($request->getHeaders())
        );

        if ($request->getBody()->getSize()) {
            $curlOptions[CURLOPT_POSTFIELDS] = $request->getBody()->__toString();
        }

        $headers = [];
        $curlOptions[CURLOPT_HEADERFUNCTION] = function ($resource, $headerString) use (&$headers) {
            $header = \trim($headerString);
            if ($header !== '') {
                $headers[] = $header;
            }

            return \mb_strlen($headerString, '8bit');
        };

        $curlResource = \curl_init($request->getUri()->__toString());
        \curl_setopt_array($curlResource, $curlOptions);

        \curl_exec($curlResource);

        $stream = Helper::copyResourceToStream($resource, $this->streamFactory);

        if ($this->options['follow_location']) {
            $headers = Helper::filterLastResponseHeaders($headers);
        }

        \fclose($resource);

        $errorNumber = \curl_errno($curlResource);
        $errorMessage = \curl_error($curlResource);

        if ($errorNumber) {
            throw new NetworkException($request, $errorMessage);
        }

        $parts = \explode(' ', \array_shift($headers), 3);
        $version = \explode('/', $parts[0])[1];
        $status = (int)$parts[1];

        \curl_close($curlResource);

        $response = $this->responseFactory->createResponse($status)
            ->withProtocolVersion($version)
            ->withBody($stream);

        foreach (Helper::deserializeHeadersToPsr7Format($headers) as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
