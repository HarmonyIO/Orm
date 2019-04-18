<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Entity\Definition\Property\Property;

class JoinedField
{
    /** @var Property */
    private $property;

    /** @var Table */
    private $table;

    /** @var string */
    private $field;

    /** @var Table */
    private $referencedTable;

    /** @var string */
    private $referencedField;

    /** @var Entity|null */
    private $entity;

    /** @var Table|null */
    private $linkTable;

    public function __construct(
        Property $property,
        Table $table,
        string $field,
        Table $referencedTable,
        string $referencedField,
        ?Entity $entity,
        ?Table $linkTable = null
    ) {
        $this->property        = $property;
        $this->table           = $table;
        $this->field           = $field;
        $this->referencedTable = $referencedTable;
        $this->referencedField = $referencedField;
        $this->entity          = $entity;
        $this->linkTable       = $linkTable;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getReferencedTable(): Table
    {
        return $this->referencedTable;
    }

    public function getReferencedField(): string
    {
        return $this->referencedField;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function getLinkTable(): ?Table
    {
        return $this->linkTable;
    }
}
