<?php

namespace Lemon\Http\Client;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property-read int  $timeout
 * @property-read bool $follow_location
 */
final class RequestOptions
{
    /** @var \Symfony\Component\OptionsResolver\OptionsResolver */
    protected $resolver;

    public function __construct(array $options = [])
    {
        $this->resolver = (new OptionsResolver())
            ->setAllowedTypes('timeout', 'integer')
            ->setAllowedTypes('follow_location', 'boolean')
            ->setDefaults([
                'follow_location' => false,
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
        return $this->resolver->isDefined($name);
    }

    /**
     * Get property's value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->resolver->offsetGet($name);
    }
}
