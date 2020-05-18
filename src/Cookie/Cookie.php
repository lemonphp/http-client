<?php

namespace Lemon\Http\Client\Cookie;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Cookie Value Object.
 *
 * @see http://tools.ietf.org/search/rfc6265
 */
final class Cookie
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var int|null
     */
    private $maxAge;

    /**
     * @var string|null
     */
    private $domain;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $secure;

    /**
     * @var bool
     */
    private $httpOnly;

    /**
     * Expires attribute is HTTP 1.0 only and should be avoided.
     *
     * @var \DateTime|null
     */
    private $expires;

    /**
     * Handles dates as defined by RFC 2616 section 3.3.1, and also some other
     * non-standard, but common formats.
     *
     * @var array
     */
    private static $dateFormats = [
        'D, d M y H:i:s T',
        'D, d M Y H:i:s T',
        'D, d-M-y H:i:s T',
        'D, d-M-Y H:i:s T',
        'D, d-m-y H:i:s T',
        'D, d-m-Y H:i:s T',
        'D M j G:i:s Y',
        'D M d H:i:s Y T',
    ];

    /**
     * Creates a cookie from a string.
     *
     * @throws TransferException
     */
    public static function parse(
        string $setCookieHeader,
        ?string $defaultDomain = null,
        ?string $defaultPath = null
    ): ?Cookie {
        $parts = \array_map('trim', \explode(';', $setCookieHeader));

        if (empty($parts) || !\strpos($parts[0], '=')) {
            return null;
        }

        list($name, $cookieValue) = self::parseKeyValue(array_shift($parts));

        $maxAge = null;
        $expires = null;
        $domain = $defaultDomain;
        $path = $defaultPath;
        $secure = false;
        $httpOnly = false;

        // Add the cookie pieces into the parsed data array
        foreach ($parts as $part) {
            list($key, $value) = self::parseKeyValue($part);

            switch (strtolower($key)) {
                case 'expires':
                    try {
                        $expires = self::parseDate($value);
                    } catch (UnexpectedValueException $e) {
                        // log error
                    }
                    break;

                case 'max-age':
                    $maxAge = (int) $value;
                    break;

                case 'domain':
                    $domain = $value;
                    break;

                case 'path':
                    $path = $value;
                    break;

                case 'secure':
                    $secure = true;
                    break;

                case 'httponly':
                    $httpOnly = true;
                    break;
            }
        }

        return new self($name, $cookieValue, $maxAge, $domain, $path, $secure, $httpOnly, $expires);
    }

    /**
     * @see https://github.com/symfony/symfony/blob/master/src/Symfony/Component/BrowserKit/Cookie.php
     *
     * @param  string $dateValue
     * @return \DateTimeInterface
     * @throws UnexpectedValueException if we cannot parse the cookie date string
     */
    private static function parseDate(string $dateValue): DateTimeInterface
    {
        foreach (self::$dateFormats as $dateFormat) {
            $date = DateTime::createFromFormat($dateFormat, $dateValue, new DateTimeZone('GMT'));
            if ($date !== false) {
                return $date;
            }
        }

        // attempt a fallback for unusual formatting
        $date = \date_create($dateValue, new DateTimeZone('GMT'));
        if ($date !== false) {
            return $date;
        }

        throw new UnexpectedValueException(\sprintf(
            'Unparseable cookie date string "%s"',
            $dateValue
        ));
    }

    /**
     * Separates key/value pair from cookie.
     *
     * @param  string $part A single cookie value in format key=value
     * @return array
     */
    private static function parseKeyValue(string $part): array
    {
        list($key, $value) = \explode('=', $part, 2);

        return [
            \trim($key),
            isset($value) ? \trim($value) : $value,
        ];
    }

    /**
     * @param string         $name
     * @param string|null    $value
     * @param int|null       $maxAge
     * @param string|null    $domain
     * @param string|null    $path
     * @param bool           $secure
     * @param bool           $httpOnly
     * @param \DateTimeInterface|null $expires  Expires attribute is HTTP 1.0 only and should be avoided.
     *
     * @throws \InvalidArgumentException if name, value or max age is not valid
     */
    public function __construct(
        string $name,
        ?string $value = null,
        ?int $maxAge = null,
        ?string $domain = null,
        ?string $path = null,
        bool $secure = false,
        bool $httpOnly = false,
        DateTimeInterface $expires = null
    ) {
        $this->validateName($name);
        $this->validateValue($value);
        $this->validateMaxAge($maxAge);

        $this->name = $name;
        $this->value = $value;
        $this->maxAge = $maxAge;
        $this->expires = $expires;
        $this->domain = $this->normalizeDomain($domain);
        $this->path = $this->normalizePath($path);
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * Creates a new cookie without any attribute validation.
     *
     * @param string         $name
     * @param string|null    $value
     * @param int            $maxAge
     * @param string|null    $domain
     * @param string|null    $path
     * @param bool           $secure
     * @param bool           $httpOnly
     * @param \DateTimeInterface|null $expires  Expires attribute is HTTP 1.0 only and should be avoided.
     */
    public static function createWithoutValidation(
        string $name,
        ?string $value = null,
        ?int $maxAge = null,
        ?string $domain = null,
        ?string $path = null,
        bool $secure = false,
        bool $httpOnly = false,
        DateTimeInterface $expires = null
    ) {
        $cookie = new self('name', null, null, $domain, $path, $secure, $httpOnly, $expires);
        $cookie->name = $name;
        $cookie->value = $value;
        $cookie->maxAge = $maxAge;

        return $cookie;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Checks if there is a value.
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    /**
     * Sets the value.
     *
     * @param  string|null $value
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withValue(?string $value): self
    {
        $this->validateValue($value);

        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * Returns the max age.
     *
     * @return int|null
     */
    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    /**
     * Checks if there is a max age.
     *
     * @return bool
     */
    public function hasMaxAge(): bool
    {
        return $this->maxAge !== null;
    }

    /**
     * Sets the max age.
     *
     * @param  int|null $maxAge
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withMaxAge(?int $maxAge): self
    {
        $this->validateMaxAge($maxAge);

        $new = clone $this;
        $new->maxAge = $maxAge;

        return $new;
    }

    /**
     * Returns the expiration time.
     *
     * @return \DateTimeInterface|null
     */
    public function getExpires(): ?DateTimeInterface
    {
        return $this->expires;
    }

    /**
     * Checks if there is an expiration time.
     *
     * @return bool
     */
    public function hasExpires(): bool
    {
        return $this->expires !== null;
    }

    /**
     * Sets the expires.
     *
     * @param  \DateTimeInterface|null $expires
     * @return self
     */
    public function withExpires(?DateTimeInterface $expires = null): self
    {
        $new = clone $this;
        $new->expires = $expires;

        return $new;
    }

    /**
     * Checks if the cookie is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->hasExpires() and $this->expires < new DateTime();
    }

    /**
     * Returns the domain.
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Checks if there is a domain.
     *
     * @return bool
     */
    public function hasDomain(): bool
    {
        return $this->domain !== null;
    }

    /**
     * Sets the domain.
     *
     * @param  string|null $domain
     * @return self
     */
    public function withDomain(?string $domain): self
    {
        $new = clone $this;
        $new->domain = $this->normalizeDomain($domain);

        return $new;
    }

    /**
     * Checks whether this cookie is meant for this domain.
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.3
     *
     * @param  string $domain
     * @return bool
     */
    public function matchDomain(string $domain): bool
    {
        // Domain is not set or exact match
        if (!$this->hasDomain() || 0 === \strcasecmp($domain, $this->domain)) {
            return true;
        }

        // Domain is not an IP address
        if (\filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }

        return (bool) \preg_match(\sprintf('/\b%s$/i', \preg_quote($this->domain)), $domain);
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the path.
     *
     * @param  string|null $path
     * @return self
     */
    public function withPath(?string $path): self
    {
        $new = clone $this;
        $new->path = $this->normalizePath($path);

        return $new;
    }

    /**
     * Checks whether this cookie is meant for this path.
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.4
     *
     * @param  string $path
     * @return bool
     */
    public function matchPath(string $path): bool
    {
        return $this->path === $path || (0 === \strpos($path, \rtrim($this->path, '/') . '/'));
    }

    /**
     * Checks whether this cookie may only be sent over HTTPS.
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Sets whether this cookie should only be sent over HTTPS.
     *
     * @param  bool $secure
     * @return self
     */
    public function withSecure(bool $secure): self
    {
        $new = clone $this;
        $new->secure = $secure;

        return $new;
    }

    /**
     * Check whether this cookie may not be accessed through Javascript.
     *
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * Sets whether this cookie may not be accessed through Javascript.
     *
     * @param  bool $httpOnly
     * @return self
     */
    public function withHttpOnly(bool $httpOnly): self
    {
        $new = clone $this;
        $new->httpOnly = $httpOnly;

        return $new;
    }

    /**
     * Checks if this cookie represents the same cookie as $cookie.
     *
     * This does not compare the values, only name, domain and path.
     *
     * @param  self $cookie
     * @return bool
     */
    public function match(self $cookie): bool
    {
        return $this->name === $cookie->name && $this->domain === $cookie->domain && $this->path === $cookie->path;
    }

    /**
     * Validates cookie attributes.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        try {
            $this->validateName($this->name);
            $this->validateValue($this->value);
            $this->validateMaxAge($this->maxAge);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Validates the name attribute.
     *
     * @see http://tools.ietf.org/search/rfc2616#section-2.2
     *
     * @param  string $name
     * @return void
     * @throws \InvalidArgumentException if the name is empty or contains invalid characters
     */
    private function validateName(string $name): void
    {
        if (empty($name)) {
            throw new InvalidArgumentException('The name cannot be empty');
        }

        // Name attribute is a token as per spec in RFC 2616
        if (preg_match('/[\x00-\x20\x22\x28-\x29\x2C\x2F\x3A-\x40\x5B-\x5D\x7B\x7D\x7F]/', $name)) {
            throw new InvalidArgumentException(\sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
    }

    /**
     * Validates a value.
     *
     * @see http://tools.ietf.org/html/rfc6265#section-4.1.1
     *
     * @param  string|null $value
     * @return void
     * @throws \InvalidArgumentException if the value contains invalid characters
     */
    private function validateValue(?string $value): void
    {
        if ($value !== null) {
            if (preg_match('/[^\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]/', $value)) {
                throw new InvalidArgumentException(
                    \sprintf('The cookie value "%s" contains invalid characters.', $value)
                );
            }
        }
    }

    /**
     * Validates a Max-Age attribute.
     *
     * @param  int|null $maxAge
     * @return void
     * @throws \InvalidArgumentException if the Max-Age is not an empty or integer value
     */
    private function validateMaxAge(?int $maxAge): void
    {
        if ($maxAge !== null) {
            if (!\is_int($maxAge)) {
                throw new InvalidArgumentException('Max-Age must be integer');
            }
        }
    }

    /**
     * Remove the leading '.' and lowercase the domain as per spec in RFC 6265.
     *
     * @see http://tools.ietf.org/html/rfc6265#section-4.1.2.3
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.3
     * @see http://tools.ietf.org/html/rfc6265#section-5.2.3
     *
     * @param  string|null $domain
     * @return string
     */
    private function normalizeDomain(?string $domain): string
    {
        if ($domain !== null) {
            $domain = \ltrim(\strtolower($domain), '.');
        }

        return $domain;
    }

    /**
     * Processes path as per spec in RFC 6265.
     *
     * @see http://tools.ietf.org/html/rfc6265#section-5.1.4
     * @see http://tools.ietf.org/html/rfc6265#section-5.2.4
     *
     * @param  string|null $path
     * @return string
     */
    private function normalizePath(?string $path): string
    {
        if ($path !== null) {
            $path = \rtrim($path, '/');
        }

        if (empty($path) || ('/' !== \substr($path, 0, 1))) {
            $path = '/';
        }

        return $path;
    }
}
