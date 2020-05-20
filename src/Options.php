<?php

namespace Lemon\Http\Client;

use ArrayAccess;
use Countable;
use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The client options
 *
 * @package     Lemon\Http\Client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 *
 * @property-read bool $follow_location
 * @property-read int  $timeout
 */
class Options implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $resolved;

    /**
     * Constructor
     *
     * @param  array $options
     */
    final public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        $this->resolved = $resolver->resolve($options);
    }

    /**
     * Check property is setted
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return \array_key_exists($name, $this->resolved);
    }

    /**
     * Get property's value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->resolved[$name] ?? null;
    }

    /**
     * Set property's value
     *
     * @param  string $name
     * @param  mixed $value
     * @throws \LogicException
     */
    public function __set($name, $value)
    {
        throw new LogicException('Client options is readonly object');
    }

    /**
     * Unset property's value
     *
     * @param  string $name
     * @param  mixed $value
     * @throws \LogicException
     */
    public function __unset($name)
    {
        throw new LogicException('Client options is readonly object');
    }

    // @codeCoverageIgnoreStart
    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->__isset((string) $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->__get((string) $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->__set((string) $offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->__unset((string) $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return \count($this->resolved);
    }
    // @codeCoverageIgnoreEnd

    /**
     * Configure options
     *
     * @param  \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'follow_location' => false,
            'timeout' => 10 * 1000,
        ]);
    }
}
