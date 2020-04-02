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
final class ClientOptions implements ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  array $options
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * Configure options
     *
     * @param  \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'follow_location' => true,
            'timeout' => 10 * 1000,
        ]);
    }

    /**
     * Check property is setted
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Get property's value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
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
        $this->offsetSet($name, $value);
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
        $this->offsetUnset($name);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->options);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->options[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException('Client options is readonly object');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        throw new LogicException('Client options is readonly object');
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->options);
    }
}
