<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

class JoinedField
{
    /** @var string */
    private $property;

    /** @var Table */
    private $table;

    /** @var string */
    private $field;

    /** @var Table */
    private $referencedTable;

    /** @var string */
    private $referencedField;

    /** @var Entity */
    private $entity;

    public function __construct(
        string $property,
        Table $table,
        string $field,
        Table $referencedTable,
        string $referencedField,
        Entity $entity
    ) {
        $this->property        = $property;
        $this->table           = $table;
        $this->field           = $field;
        $this->referencedTable = $referencedTable;
        $this->referencedField = $referencedField;
        $this->entity          = $entity;
    }

    public function getProperty(): string
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

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
