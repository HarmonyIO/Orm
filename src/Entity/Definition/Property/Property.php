<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Property;

use HarmonyIO\Orm\Entity\Definition\Relation\Relation;

class Property
{
    /** @var string */
    private $name;

    /** @var string */
    private $column;

    /** @var Relation|null */
    private $relation;

    public function __construct(string $name, string $column, ?Relation $relation = null)
    {
        $this->name     = $name;
        $this->column   = $column;
        $this->relation = $relation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function hasRelation(): bool
    {
        return $this->relation !== null;
    }

    public function getRelation(): ?Relation
    {
        return $this->relation;
    }
}
