<?php

namespace App\Model;

use InvalidArgumentException;
use Iterator;

final class CitiesCollection implements Iterator
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param string $filename
     *
     * @throws \JsonException
     */
    public function __construct(string $filename)
    {
        if (false === file_exists($filename)) {
            throw new InvalidArgumentException('File does not exists: ' . $filename);
        }

        $this->data = json_decode(file_get_contents($filename), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return key($this->data);
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return null !== $this->current();
    }

    /**
     * @inheritDoc
     */
    public function current(): ?City
    {
        return is_array(current($this->data)) ? City::createFromArray(current($this->data)) : null;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        reset($this->data);
    }
}
