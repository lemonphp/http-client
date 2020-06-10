<?php

namespace Lemon\Http\Client\History;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Lemon\Http\Client\JournalInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Traversable;

/**
 * The journal class
 *
 * @package     lemonphp/http-client
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class Journal implements JournalInterface, Countable, IteratorAggregate
{
    /**
     * @var array|\Lemon\Http\Client\History\Entry[]
     */
    private $entries;

    /**
     * @var int
     */
    private $limit;

    /**
     * Constructor
     *
     * @param int $limit
     */
    public function __construct(int $limit = 10)
    {
        $this->limit = $limit;
        $this->entries = [];
    }

    /**
     * Records an entry in the journal.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  The request
     * @param  \Psr\Http\Message\ResponseInterface $response The response
     * @param  float|null        $duration The duration in seconds
     */
    public function record(RequestInterface $request, ResponseInterface $response, float $duration = null): void
    {
        $this->addEntry(new Entry($request, $response, $duration));
    }

    /**
     * Add new entry
     *
     * @param  \Lemon\Http\Client\History\Entry $entry
     * @return void
     */
    public function addEntry(Entry $entry): void
    {
        \array_push($this->entries, $entry);

        if ($this->getLimit() > 0) {
            $this->entries = \array_slice($this->entries, $this->getLimit() * -1);
        }
        end($this->entries);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->entries = [];
    }

    /**
     * @param  int $limit
     * @return void
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return array|\Lemon\Http\Client\History\Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return \Lemon\Http\Client\History\Entry|null
     */
    public function getLastEntry(): ?Entry
    {
        $entry = end($this->entries);

        return false === $entry ? null : $entry;
    }

    /**
     * Get last sent request
     *
     * @return \Psr\Http\Message\RequestInterface|null
     */
    public function getLastRequest(): ?RequestInterface
    {
        $entry = $this->getLastEntry();
        if (null === $entry) {
            return null;
        }

        return $entry->getRequest();
    }

    /**
     * Get last received response
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getLastResponse(): ?ResponseInterface
    {
        $entry = $this->getLastEntry();
        if (null === $entry) {
            return null;
        }

        return $entry->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->entries);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator(\array_reverse($this->entries));
    }
}
