<?php

namespace Lemon\Http\Client\Authentication;

use InvalidArgumentException;
use Lemon\Http\Client\AuthenticationInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * OAuth 1.0 authentication
 *
 * @see http://oauth.net/core/1.0/#rfc.section.9.1.1 OAuth specification
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class OAuth1 implements AuthenticationInterface
{
    /**
     * Consumer request method constants.
     *
     * @see http://oauth.net/core/1.0/#consumer_req_param
     */
    public const REQUEST_METHOD_HEADER = 'header';
    public const REQUEST_METHOD_QUERY  = 'query';

    public const SIGNATURE_METHOD_HMAC      = 'HMAC-SHA1';
    public const SIGNATURE_METHOD_RSA       = 'RSA-SHA1';
    public const SIGNATURE_METHOD_PLAINTEXT = 'PLAINTEXT';

    /**
     * @var array
     */
    protected $options;

    /**
     * Create a new OAuth 1.0 plugin.
     *
     * @see OAuth1::configureOptions()
     *
     * @param array $options Configuration array.
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Authenticate
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\RequestInterface
     * @throws \InvalidArgumentException
     */
    public function authenticate(RequestInterface $request): RequestInterface
    {
        // OAuth parameters
        $oauthParams = $this->getOauthParams($this->generateNonce());

        // OAuth signature
        $oauthParams['oauth_signature'] = $this->getSignature($request, $oauthParams);

        // Sort parameters
        \uksort($oauthparams, 'strcmp');

        switch ($this->options['request_method']) {
            case self::REQUEST_METHOD_HEADER:
                $request = $this->addAuthorizationHeader($request, $oauthParams);
                break;
            case self::REQUEST_METHOD_QUERY:
                $request = $this->addAuthorizationQuery($request, $oauthParams);
                break;
            default:
                throw new InvalidArgumentException(
                    \sprintf('Invalid consumer method "%s"', $this->options['request_method'])
                );
        }

        return $request;
    }

    /**
     * Configure options
     *
     * - consumer_key: Consumer key string. This parameter is required.
     * - consumer_secret: Consumer secret. This parameter is required.
     * - callback: OAuth callback uri.
     * - request_method: Consumer request method. One of 'header' or 'query'. Defaults to 'header'.
     * - private_key_file: The location of your private key file (RSA-SHA1 signature method only)
     * - private_key_passphrase: The passphrase for your private key file (RSA-SHA1 signature method only)
     * - token: Client token
     * - token_secret: Client secret token
     * - verifier: OAuth verifier.
     * - version: OAuth version. Defaults to '1.0'.
     * - realm: OAuth realm.
     * - signature_method: Signature method. One of 'HMAC-SHA1', 'RSA-SHA1', or 'PLAINTEXT'. Defaults to 'HMAC-SHA1'.
     *
     * @param  \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // consumer_key
            ->define('consumer_key')
            ->required()
            ->allowedTypes('string')
            ->info('Consumer key string')

            // consumer_secret
            ->define('consumer_secret')
            ->required()
            ->allowedTypes('string')
            ->info('Consumer secret string')

            // callback
            ->define('callback')
            ->allowedTypes('string')
            ->info('OAuth callback URI')

            // request_method
            ->define('request_method')
            ->allowedValues([
                self::REQUEST_METHOD_HEADER,
                self::REQUEST_METHOD_QUERY,
            ])
            ->default(self::REQUEST_METHOD_HEADER)

            // private key for RSA
            ->define('private_key_file')->allowedTypes('string')
            ->define('private_key_passphrase')->allowedTypes('string')->default('')

            // token, secret and verifier
            ->define('token')->allowedTypes('string')
            ->define('token_secret')->allowedTypes('string')
            ->define('verifier')->allowedTypes('string')

            // version
            ->define('version')
            ->allowedTypes('string')
            ->default('1.0')

            // realm
            ->define('realm')->allowedTypes('string')

            // signature_method
            ->define('signature_method')
            ->allowedValues([
                self::SIGNATURE_METHOD_HMAC,
                self::SIGNATURE_METHOD_RSA,
                self::SIGNATURE_METHOD_PLAINTEXT,
            ])
            ->default(self::SIGNATURE_METHOD_HMAC)
        ;
    }

    /**
     * Calculate signature for request
     *
     * @param  \Psr\Http\Message\RequestInterface $request Request to generate a signature for
     * @param  array                              $params  Oauth parameters.
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSignature(RequestInterface $request, array $params): string
    {
        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        unset($params['oauth_signature']);

        // Add POST fields if the request uses POST fields and no files
        if ($request->getHeaderLine('Content-Type') === 'application/x-www-form-urlencoded') {
            \parse_str($request->getBody()->getContents(), $data);
            $params += $data;
        }

        // Parse & add query string parameters as base string parameters
        \parse_str($request->getUri()->getQuery(), $query);
        $params += $query;

        $baseString = $this->createBaseString(
            $request,
            $this->prepareParameters($params)
        );

        // Implements double-dispatch to sign requests
        switch ($this->options['signature_method']) {
            case self::SIGNATURE_METHOD_RSA:
                $signature = $this->signUsingRsaSha1($baseString);
                break;
            case self::SIGNATURE_METHOD_PLAINTEXT:
                $signature = $this->signUsingPlaintext($baseString);
                break;
            case self::SIGNATURE_METHOD_HMAC:
                $signature = $this->signUsingHmacSha1($baseString);
                break;
            default:
                throw new InvalidArgumentException(
                    \sprintf('Invalid signature method "%s"', $this->options['signature_method'])
                );
        }

        return \base64_encode($signature);
    }

    /**
     * Creates the Signature Base String.
     *
     * The Signature Base String is a consistent reproducible concatenation of
     * the request elements into a single string. The string is used as an
     * input in hashing or signing algorithms.
     *
     * @see http://oauth.net/core/1.0/#sig_base_example
     *
     * @param  \Psr\Http\Message\RequestInterface $request Request being signed
     * @param  array                              $params  Associative array of OAuth parameters
     * @return string
     */
    protected function createBaseString(RequestInterface $request, array $params): string
    {
        // Remove query params from URL. Ref: Spec: 9.1.2.
        $url = $request->getUri()->withQuery('');
        $query = \http_build_query($params, '', '&', \PHP_QUERY_RFC3986);

        return \strtoupper($request->getMethod())
            . '&' . \rawurlencode($url)
            . '&' . \rawurlencode($query);
    }

    /**
     * Generate nonce
     *
     * @see https://stackoverflow.com/questions/18910814/.../31419246#31419246
     *
     * @return string
     */
    private function generateNonce(): string
    {
        return \bin2hex(\random_bytes(16));
    }

    /**
     * Get the oauth parameters as named by the oauth spec
     *
     * @param  string $nonce  Unique nonce
     * @return array
     */
    private function getOauthParams($nonce)
    {
        $params = [
            'oauth_consumer_key'     => $this->options['consumer_key'],
            'oauth_nonce'            => $nonce,
            'oauth_signature_method' => $this->options['signature_method'],
            'oauth_timestamp'        => \time(),
        ];

        // Optional parameters should not be set if they have not been set in
        // the config as the parameter may be considered invalid by the Oauth
        // service.
        $optionalParams = [
            'callback'  => 'oauth_callback',
            'token'     => 'oauth_token',
            'verifier'  => 'oauth_verifier',
            'version'   => 'oauth_version'
        ];

        foreach ($optionalParams as $optionName => $oauthName) {
            if (isset($this->options[$optionName])) {
                $params[$oauthName] = $this->options[$optionName];
            }
        }

        return $params;
    }

    /**
     * Convert booleans to strings, removed unset parameters, and sorts the array
     *
     * @param  array $data Data array
     * @return array
     */
    private function prepareParameters(array $data): array
    {
        // Parameters are sorted by name, using lexicographical byte value
        // ordering. Ref: Spec: 9.1.1 (1).
        \uksort($data, 'strcmp');

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @param  string $baseString
     * @return string
     */
    private function signUsingHmacSha1(string $baseString): string
    {
        $parts = [\rawurlencode($this->options['consumer_secret'])];
        if (isset($this->options['token_secret'])) {
            $parts[] = \rawurlencode($this->options['token_secret']);
        }

        return \hash_hmac('sha1', $baseString, \implode('&', $parts), true);
    }

    /**
     * @param  string $baseString
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function signUsingRsaSha1(string $baseString): string
    {
        if (!\function_exists('openssl_pkey_get_private')) {
            throw new RuntimeException(
                'RSA-SHA1 signature method requires the OpenSSL extension.'
            );
        }

        // validate options
        if (
            !isset($this->options['private_key_file']) ||
            !isset($this->options['private_key_passphrase']) ||
            !\is_readable($this->options['private_key_file'])
        ) {
            throw new InvalidArgumentException(
                'RSA-SHA1 signature method requires option "private_key_file" and "private_key_passphrase".'
            );
        }

        $privateKey = \openssl_pkey_get_private(
            'file://' . \realpath($this->options['private_key_file']),
            $this->options['private_key_passphrase']
        );

        if ($privateKey === false) {
            throw new RuntimeException('Cannot load private key');
        }

        $signature = '';
        \openssl_sign($baseString, $signature, $privateKey);
        \openssl_free_key($privateKey);

        return $signature;
    }

    /**
     * @param  string $baseString
     * @return string
     */
    private function signUsingPlaintext(string $baseString): string
    {
        return $baseString;
    }

    /**
     * Add the Authorization header for a request
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  array                              $params Associative array of authorization parameters.
     * @return \Psr\Http\Message\RequestInterface
     */
    private function addAuthorizationHeader(RequestInterface $request, array $params): RequestInterface
    {
        foreach ($params as $key => $value) {
            $params[$key] = $key . '="' . \rawurlencode($value) . '"';
        }

        if (isset($this->options['realm'])) {
            \array_unshift(
                $params,
                'realm="' . \rawurlencode($this->options['realm']) . '"'
            );
        }

        $authorizationHeader = \sprintf('OAuth %s', \implode(', ', $params));

        return $request->withHeader('Authorization', $authorizationHeader);
    }

    /**
     * Add the Authorization query for a request
     *
     * @param  \Psr\Http\Message\RequestInterface $request
     * @param  array                              $params Associative array of authorization parameters.
     * @return \Psr\Http\Message\RequestInterface
     */
    private function addAuthorizationQuery(RequestInterface $request, array $params): RequestInterface
    {
        \parse_str($request->getUri()->getQuery(), $query);

        $uri = $request->getUri()->withQuery(
            \http_build_query($params + $query, null, '&', \PHP_QUERY_RFC3986)
        );

        return $request->withUri($uri);
    }
}
