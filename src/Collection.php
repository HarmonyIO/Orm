<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

use HarmonyIO\Orm\Entity\Entity;

class Collection implements \Iterator, \Countable
{
    /** @var Entity[] */
    private $entities = [];

    // @todo: we might want to key by id here so other operations are O(1) in userland instead of O(n)
    public function __construct(Entity ...$entities)
    {
        $this->entities = $entities;
    }

    public function add(Entity $entity): void
    {
        if ($this->contains($entity)) {
            return;
        }

        $this->entities[] = $entity;
    }

    public function remove(Entity $entity): void
    {
        foreach ($this->entities as $index => $targetEntity) {
            if ($targetEntity->getId() !== $entity->getId()) {
                continue;
            }

            unset($this->entities[$index]);

            return;
        }
    }

    /**
     * @todo: loose comparison does what we want in terms of comparing object values and properties
     *        however we do not actually want the values to be loose compared
     */
    public function contains(Entity $entity): bool
    {
        foreach ($this->entities as $targetEntity) {
            //phpcs:ignore SlevomatCodingStandard.ControlStructures.DisallowEqualOperators.DisallowedEqualOperator
            if ($targetEntity == $entity) {
                return true;
            }
        }

        return false;
    }

    public function current(): Entity
    {
        return current($this->entities);
    }

    public function next(): void
    {
        next($this->entities);
    }

    public function key(): ?int
    {
        return key($this->entities);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->entities);
    }

    public function count(): int
    {
        return count($this->entities);
    }
}
