<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Property;

class PropertyCollection implements \Iterator
{
    /** @var Property[] */
    private $properties = [];

    public function __construct(Property ...$properties)
    {
        $this->properties = $properties;
    }

    public function current(): Property
    {
        return current($this->properties);
    }

    public function next(): void
    {
        next($this->properties);
    }

    public function key(): ?int
    {
        return key($this->properties);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->properties);
    }
}
