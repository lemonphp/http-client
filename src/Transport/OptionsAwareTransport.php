<?php

namespace Lemon\Http\Client\Transport;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setDefaults([
            'follow_location' => false,
            'timeout' => 10 * 1000,
        ]);
    }
}
