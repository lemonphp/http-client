<?php

namespace Lemon\Http\Client\Transport;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The options aware transport trait
 *
 * Allow configure transport options
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
trait OptionsAwareTransport
{
    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory
     * @param \Psr\Http\Message\ResponseFactoryInterface $responseFactory
     * @param array $options
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory,
        array $options = []
    ) {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;

        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Configure options
     *
     * @param  Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // follow_location
            ->define('follow_location')
            ->allowedTypes('bool')
            ->default(false)
            ->info('Allow follow location redirecting')

            // timeout
            ->define('timeout')
            ->allowedTypes('int')
            ->default(10000)
            ->info('Request timeout in millinsecounds. Default 10000')
        ;
    }
}
